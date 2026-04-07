<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TherapistLeaveRequest;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapistLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = TherapistLeaveRequest::with('therapist', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('therapist_id')) {
            $query->where('therapist_id', $request->therapist_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        $leaves     = $query->orderBy('created_at', 'desc')->paginate(15);
        $therapists = Therapist::orderBy('name')->get();

        // Summary dari DB agar akurat lintas halaman
        $summaryQuery = TherapistLeaveRequest::query();
        if ($request->filled('therapist_id')) {
            $summaryQuery->where('therapist_id', $request->therapist_id);
        }
        if ($request->filled('status')) {
            $summaryQuery->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $summaryQuery->whereMonth('start_date', $request->month);
        }

        $summary = [
            'pending'  => (clone $summaryQuery)->where('status', 'pending')->count(),
            'approved' => (clone $summaryQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $summaryQuery)->where('status', 'rejected')->count(),
            'total'    => $summaryQuery->count(),
        ];

        return view('admin.leaves.index', compact('leaves', 'therapists', 'summary'));
    }

    public function show(TherapistLeaveRequest $leaveRequest)
    {
        $leaveRequest->load('therapist', 'approver');

        return view('admin.leaves.show', compact('leaveRequest'));
    }

    public function approve(Request $request, TherapistLeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya izin dengan status pending yang bisa disetujui.']);
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status'         => 'approved',
            'approved_by'    => Auth::id(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'approved_at'    => now(),
        ]);

        return back()->with('success', 'Pengajuan izin berhasil disetujui.');
    }

    public function reject(Request $request, TherapistLeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya izin dengan status pending yang bisa ditolak.']);
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string|min:10|max:500',
        ]);

        $leaveRequest->update([
            'status'         => 'rejected',
            'approved_by'    => Auth::id(),
            'approval_notes' => $validated['approval_notes'],
            'approved_at'    => now(),
        ]);

        return back()->with('success', 'Pengajuan izin berhasil ditolak.');
    }

    public function destroy(TherapistLeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }

    public static function getPendingLeaves($limit = 5)
    {
        return TherapistLeaveRequest::pending()
            ->with('therapist')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getLeavesSummary()
    {
        return [
            'pending'  => TherapistLeaveRequest::pending()->count(),
            'approved' => TherapistLeaveRequest::approved()->count(),
            'rejected' => TherapistLeaveRequest::rejected()->count(),
            'total'    => TherapistLeaveRequest::count(),
        ];
    }

    public function getTherapistActiveLeaves(Therapist $therapist)
    {
        $leaves = $therapist->leaveRequests()
            ->approved()
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $leaves->map(fn($l) => [
                'id'         => $l->id,
                'type'       => $l->type,
                'start_date' => $l->start_date->format('Y-m-d'),
                'end_date'   => $l->end_date->format('Y-m-d'),
                'duration'   => $l->day_count,
                'reason'     => $l->reason,
            ]),
        ]);
    }
}
