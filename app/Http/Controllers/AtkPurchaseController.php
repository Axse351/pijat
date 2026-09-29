<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\AtkCategory;
use App\Models\AtkPurchase;
use App\Models\AtkStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AtkPurchaseController extends Controller
{
    /**
     * Tampilkan daftar pembelian ATK (admin & kasir)
     */
    public function index(Request $request)
    {
        $query = AtkPurchase::with(['atk.category', 'createdBy'])
            ->orderBy('purchase_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate   = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('atk', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('atk', function ($q) use ($request) {
                $q->where('atk_category_id', $request->category_id);
            });
        }

        $purchases  = $query->paginate(15);
        $statuses   = ['completed' => 'Selesai', 'pending' => 'Pending', 'cancelled' => 'Batal'];
        $categories = AtkCategory::orderBy('name')->get();

        return view('admin.atk_purchases_index', compact('purchases', 'statuses', 'categories'));
    }

    /**
     * Form input pembelian ATK (admin & kasir)
     */
    public function create()
    {
        $categories = AtkCategory::with('atks')->get();

        return view('admin.atk_purchases_create', compact('categories'));
    }

    /**
     * Simpan pembelian ATK (admin & kasir)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'atk_id'         => 'required|exists:atks,id',
            'quantity'       => 'required|integer|min:1',
            'unit_price'     => 'required|numeric|min:0',
            'purchase_date'  => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
            $validated['created_by']  = Auth::id();
            $validated['status']      = 'completed';

            $purchase = AtkPurchase::create($validated);

            $atk         = Atk::findOrFail($validated['atk_id']);
            $stockBefore = $atk->stock;
            $stockAfter  = $atk->stock + $validated['quantity'];

            $atk->update([
                'stock'               => $stockAfter,
                'last_purchase_price' => $validated['unit_price'],
            ]);

            AtkStockHistory::create([
                'atk_id'          => $validated['atk_id'],
                'quantity_before' => $stockBefore,
                'quantity_after'  => $stockAfter,
                'quantity_change' => $validated['quantity'],
                'type'            => 'in',
                'user_id'         => Auth::id(),
                'notes'           => 'Pembelian: ' . ($purchase->receipt_number ?: '-'),
            ]);

            // Record pengurang pendapatan (Opex)
            $purchase->recordOpex();

            DB::commit();

            return redirect()->route('admin.atk-purchases.show', $purchase)
                ->with('success', 'Pembelian ATK berhasil dicatat dan dikurangi dari pendapatan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Detail pembelian ATK (admin & kasir)
     */
    public function show(AtkPurchase $purchase)
    {
        $purchase->load(['atk.category', 'createdBy', 'opex']);

        return view('admin.atk_purchases_show', compact('purchase'));
    }

    /**
     * Form edit pembelian ATK (admin only)
     */
    public function edit(AtkPurchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pembelian dengan status pending yang dapat diedit']);
        }

        $atks = Atk::orderBy('name')->get();

        return view('admin.atk_purchases_edit', compact('purchase', 'atks'));
    }

    /**
     * Update pembelian ATK (admin only)
     */
    public function update(Request $request, AtkPurchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pembelian dengan status pending yang dapat diedit']);
        }

        $validated = $request->validate([
            'quantity'       => 'required|integer|min:1',
            'unit_price'     => 'required|numeric|min:0',
            'purchase_date'  => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
            $purchase->update($validated);

            DB::commit();

            return redirect()->route('admin.atk-purchases.show', $purchase)
                ->with('success', 'Pembelian ATK berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus pembelian ATK (admin only)
     * Hanya boleh untuk status pending / cancelled agar stok & opex tidak rusak.
     */
    public function destroy(AtkPurchase $purchase)
    {
        if ($purchase->status === 'completed') {
            return back()->withErrors(['error' => 'Pembelian yang sudah selesai tidak dapat dihapus. Batalkan terlebih dahulu.']);
        }

        $purchase->delete();

        return redirect()->route('admin.atk-purchases.index')
            ->with('success', 'Pembelian ATK berhasil dihapus!');
    }

    /**
     * Konfirmasi/selesaikan pembelian (admin only)
     */
    public function confirm(Request $request, AtkPurchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->withErrors(['error' => 'Status pembelian tidak dapat dikonfirmasi']);
        }

        try {
            DB::beginTransaction();

            $atk         = $purchase->atk;
            $stockBefore = $atk->stock;
            $stockAfter  = $atk->stock + $purchase->quantity;

            $atk->update([
                'stock'               => $stockAfter,
                'last_purchase_price' => $purchase->unit_price,
            ]);

            AtkStockHistory::create([
                'atk_id'          => $purchase->atk_id,
                'quantity_before' => $stockBefore,
                'quantity_after'  => $stockAfter,
                'quantity_change' => $purchase->quantity,
                'type'            => 'in',
                'user_id'         => Auth::id(),
                'notes'           => 'Konfirmasi pembelian: ' . ($purchase->receipt_number ?: '-'),
            ]);

            $purchase->update(['status' => 'completed']);

            $purchase->recordOpex();

            DB::commit();

            return redirect()->route('admin.atk-purchases.show', $purchase)
                ->with('success', 'Pembelian ATK berhasil dikonfirmasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Batalkan pembelian ATK (admin only)
     */
    public function cancel(Request $request, AtkPurchase $purchase)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            if ($purchase->status === 'completed') {
                $atk         = $purchase->atk;
                $stockBefore = $atk->stock;
                $stockAfter  = max(0, $atk->stock - $purchase->quantity);

                $atk->update(['stock' => $stockAfter]);

                AtkStockHistory::create([
                    'atk_id'          => $purchase->atk_id,
                    'quantity_before' => $stockBefore,
                    'quantity_after'  => $stockAfter,
                    'quantity_change' => -$purchase->quantity,
                    'type'            => 'return',
                    'user_id'         => Auth::id(),
                    'notes'           => "Pembatalan pembelian: {$request->reason}",
                ]);

                $purchase->reverseOpex();
            }

            $purchase->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('admin.atk-purchases.index')
                ->with('success', 'Pembelian ATK berhasil dibatalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Export laporan pembelian ATK
     */
    public function exportReport(Request $request)
    {
        $query = AtkPurchase::with(['atk.category', 'createdBy'])
            ->where('status', 'completed');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate   = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();

        return view('atk.purchases.report', compact('purchases'));
    }

    /**
     * Get ATK items berdasarkan kategori (AJAX, admin & kasir)
     */
    public function getAtkByCategory(AtkCategory $category)
    {
        $atks = $category->atks()
            ->select('id', 'name', 'code', 'last_purchase_price', 'stock')
            ->get();

        return response()->json($atks);
    }

    /**
     * Get detail ATK (AJAX, admin & kasir)
     */
    public function getAtkDetail(Atk $atk)
    {
        return response()->json([
            'id'                  => $atk->id,
            'name'                => $atk->name,
            'code'                => $atk->code,
            'stock'               => $atk->stock,
            'last_purchase_price' => $atk->last_purchase_price,
            'category'            => $atk->category->name,
        ]);
    }
}
