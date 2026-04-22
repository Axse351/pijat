<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Therapist;
use App\Services\CommissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function __construct(private CommissionService $commissionService) {}

    // ── INDEX ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $therapistId = $request->get('therapist_id');
        $status      = $request->get('status', 'all');
        $weekStart   = $request->get('week_start');
        $source      = $request->get('source', 'all'); // all | normal | cancel_forfeit

        // ── Query komisi ──────────────────────────────────────────────────
        $query = Commission::with(['booking.customer', 'booking.service', 'therapist'])
            ->orderBy('created_at', 'desc');

        if ($therapistId) {
            $query->where('therapist_id', $therapistId);
        }

        if ($status === 'unpaid') {
            $query->where('is_paid', false);
        } elseif ($status === 'paid') {
            $query->where('is_paid', true);
        }

        if ($weekStart) {
            $wStart = Carbon::parse($weekStart)->startOfWeek();
            $wEnd   = $wStart->copy()->endOfWeek();
            $query->whereBetween('week_start', [$wStart->toDateString(), $wEnd->toDateString()]);
        }

        if ($source === 'normal') {
            $query->where('commission_source', 'normal');
        } elseif ($source === 'cancel_forfeit') {
            $query->where('commission_source', 'cancel_forfeit');
        }

        $commissions = $query->paginate(20)->withQueryString();
        $therapists  = Therapist::where('is_active', true)->orderBy('name')->get();

        // ── Summary tagihan belum bayar per terapis ───────────────────────
        $summaryUnpaid = Commission::where('is_paid', false)
            ->selectRaw('
                therapist_id,
                SUM(commission_amount) as total,
                COUNT(*) as count,
                SUM(CASE WHEN commission_source = "normal" THEN commission_amount ELSE 0 END) as from_sessions,
                SUM(CASE WHEN commission_source = "cancel_forfeit" THEN commission_amount ELSE 0 END) as from_cancels
            ')
            ->groupBy('therapist_id')
            ->with('therapist')
            ->get();

        // ── Grand total unpaid ────────────────────────────────────────────
        $grandTotalUnpaid = $summaryUnpaid->sum('total');

        return view('admin.commissions.index', compact(
            'commissions',
            'therapists',
            'therapistId',
            'status',
            'weekStart',
            'source',
            'summaryUnpaid',
            'grandTotalUnpaid',
        ));
    }

    // ── THERAPIST SUMMARY ─────────────────────────────────────────────────
    public function therapistSummary(Therapist $therapist)
    {
        $unpaid = $this->commissionService->getUnpaidTotal($therapist->id);

        $history = Commission::where('therapist_id', $therapist->id)
            ->with(['booking.service', 'booking.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.commissions.therapist', compact('therapist', 'unpaid', 'history'));
    }

    // ── MARK PAID ─────────────────────────────────────────────────────────
    public function markPaid(Commission $commission)
    {
        $commission->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', "Komisi Rp " . number_format($commission->commission_amount, 0, ',', '.') . " berhasil ditandai lunas.");
    }

    // ── BULK PAID ─────────────────────────────────────────────────────────
    public function markBulkPaid(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'week_start'   => 'nullable|date',
        ]);

        $query = Commission::where('therapist_id', $request->therapist_id)
            ->where('is_paid', false);

        if ($request->week_start) {
            $wStart = Carbon::parse($request->week_start)->startOfWeek();
            $wEnd   = $wStart->copy()->endOfWeek();
            $query->whereBetween('week_start', [$wStart->toDateString(), $wEnd->toDateString()]);
        }

        $total = $query->sum('commission_amount');
        $count = $query->count();

        $query->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', "{$count} komisi (Rp " . number_format($total, 0, ',', '.') . ") berhasil ditandai lunas.");
    }
}
