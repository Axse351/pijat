<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * Daftar komisi per terapis, dikelompokkan per minggu
     */
    public function index(Request $request)
    {
        $therapistId = $request->input('therapist_id');
        $status      = $request->input('status', 'all'); // all | unpaid | paid
        $weekStart   = $request->input('week_start');

        $query = Commission::with(['booking.customer', 'booking.service', 'therapist'])
            ->orderByDesc('week_start')
            ->orderByDesc('created_at');

        if ($therapistId) {
            $query->where('therapist_id', $therapistId);
        }

        if ($status === 'unpaid') {
            $query->where('is_paid', false);
        } elseif ($status === 'paid') {
            $query->where('is_paid', true);
        }

        if ($weekStart) {
            $query->where('week_start', $weekStart);
        }

        $commissions = $query->paginate(30)->withQueryString();
        $therapists  = Therapist::orderBy('name')->get();

        // Summary per terapis (unpaid)
        $summaryUnpaid = Commission::with('therapist')
            ->where('is_paid', false)
            ->selectRaw('therapist_id, SUM(commission_amount) as total, COUNT(*) as count')
            ->groupBy('therapist_id')
            ->with('therapist')
            ->get();

        return view('admin.commissions.index', compact(
            'commissions',
            'therapists',
            'summaryUnpaid',
            'therapistId',
            'status',
            'weekStart'
        ));
    }

    /**
     * Tandai 1 komisi sebagai sudah dibayar
     */
    public function markPaid(Commission $commission)
    {
        $commission->update(['is_paid' => true]);

        return back()->with('success', 'Komisi ditandai sudah dibayar.');
    }

    /**
     * Tandai SEMUA komisi belum bayar milik terapis X di minggu Y sebagai sudah dibayar
     */
    public function markBulkPaid(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'week_start'   => 'required|date',
        ]);

        $updated = Commission::where('therapist_id', $request->therapist_id)
            ->where('week_start', $request->week_start)
            ->where('is_paid', false)
            ->update(['is_paid' => true]);

        $therapist = Therapist::find($request->therapist_id);

        return back()->with('success', "✅ {$updated} komisi {$therapist->name} minggu ini ditandai lunas.");
    }

    /**
     * Ringkasan per terapis (untuk halaman terapis edit — opsional)
     */
    public function therapistSummary(Therapist $therapist)
    {
        $unpaid = Commission::where('therapist_id', $therapist->id)
            ->where('is_paid', false)
            ->sum('commission_amount');

        $paid = Commission::where('therapist_id', $therapist->id)
            ->where('is_paid', true)
            ->sum('commission_amount');

        $weekly = Commission::where('therapist_id', $therapist->id)
            ->selectRaw('week_start, week_end, SUM(commission_amount) as total, SUM(is_paid) as paid_count, COUNT(*) as total_count')
            ->groupBy('week_start', 'week_end')
            ->orderByDesc('week_start')
            ->get();

        return view('admin.commissions.therapist', compact('therapist', 'unpaid', 'paid', 'weekly'));
    }
}
