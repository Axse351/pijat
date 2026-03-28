<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\AtkCategory;
use App\Models\AtkStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AtkController extends Controller
{
    public function index(Request $request)
    {
        $query = Atk::with('category')->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('atk_category_id', $request->category_id);
        }

        if ($request->filled('low_stock')) {
            $query->where('stock', '<', 5);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $atks       = $query->paginate(20);
        $categories = AtkCategory::all();

        return view('admin.atk_items_index', compact('atks', 'categories'));
    }

    public function create()
    {
        $categories = AtkCategory::all();

        return view('admin.atk_items_create_edit', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'atk_category_id' => 'required|exists:atk_categories,id',
            'name'            => 'required|string|unique:atks',
            'code'            => 'required|string|unique:atks',
            'description'     => 'nullable|string',
            'stock'               => 'nullable|integer|min:0',
            'last_purchase_price' => 'nullable|numeric|min:0',
        ]);

        $validated['stock'] = $request->input('stock', 0);
        $validated['last_purchase_price'] = $request->input('last_purchase_price', null);

        Atk::create($validated);

        return redirect()->route('admin.atk-items.index')
            ->with('success', 'Item COA berhasil ditambahkan!');
    }

    public function show(Atk $atk)
    {
        $atk->load('category', 'purchases', 'stockHistories');

        $recentTransactions = $atk->stockHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPurchases = $atk->purchases()
            ->with('createdBy')
            ->orderBy('purchase_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.atk_items_show', compact('atk', 'recentTransactions', 'recentPurchases'));
    }

    public function edit(Atk $atk)
    {
        $categories = AtkCategory::all();

        return view('admin.atk_items_create_edit', compact('atk', 'categories'));
    }

    public function update(Request $request, Atk $atk)
    {
        $validated = $request->validate([
            'atk_category_id' => 'required|exists:atk_categories,id',
            'name'            => "required|string|unique:atks,name,{$atk->id}",
            'code'            => "required|string|unique:atks,code,{$atk->id}",
            'description'     => 'nullable|string',
            'stock'               => 'nullable|integer|min:0',
            'last_purchase_price' => 'nullable|numeric|min:0',
        ]);

        $atk->update($validated);

        return redirect()->route('admin.atk-items.show', $atk)
            ->with('success', 'Item COA berhasil diperbarui!');
    }

    public function destroy(Atk $atk)
    {
        if ($atk->purchases()->exists()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus item yang memiliki history pembelian']);
        }

        $atk->delete();

        return redirect()->route('admin.atk-items.index')
            ->with('success', 'Item COA berhasil dihapus!');
    }

    public function adjustStock(Request $request, Atk $atk)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type'     => 'required|in:adjustment,return',
            'notes'    => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $stockBefore = $atk->stock;
            $stockAfter  = max(0, $atk->stock + $validated['quantity']);

            $atk->update(['stock' => $stockAfter]);

            AtkStockHistory::create([
                'atk_id'          => $atk->id,
                'quantity_before' => $stockBefore,
                'quantity_after'  => $stockAfter,
                'quantity_change' => $validated['quantity'],
                'type'            => $validated['type'],
                'user_id'         => Auth::id(),
                'notes'           => $validated['notes'],
            ]);

            DB::commit();

            return redirect()->route('admin.atk-items.show', $atk)
                ->with('success', 'Penyesuaian stok berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
