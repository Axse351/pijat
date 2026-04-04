<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TherapistLeaveRequest;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk mengelola pengajuan izin terapis
 * 
 * Tambahkan ke: app/Http/Controllers/Admin/TherapistLeaveController.php
 */
class TherapistLeaveController extends Controller
{
    /**
     * Tampilkan daftar pengajuan izin
     */
    public function index(Request $request)
    {
        $query = TherapistLeaveRequest::with('therapist', 'approver');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan terapis
        if ($request->has('therapist_id') && $request->therapist_id !== '') {
            $query->where('therapist_id', $request->therapist_id);
        }

        // Filter berdasarkan bulan
        if ($request->has('month') && $request->month !== '') {
            $month = $request->month;
            $query->whereMonth('start_date', $month);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);
        $therapists = Therapist::orderBy('name')->get();

        return view('admin.leaves.index', [
            'leaves'     => $leaves,
            'therapists' => $therapists,
        ]);
    }

    /**
     * Tampilkan detail pengajuan izin
     */
    public function show(TherapistLeaveRequest $leaveRequest)
    {
        return view('admin.leaves.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Approve pengajuan izin
     */
    public function approve(Request $request, TherapistLeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya izin dengan status pending yang bisa disetujui.']);
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status'       => 'approved',
            'approved_by'  => Auth::id(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'approved_at'  => now(),
        ]);

        return back()->with('success', 'Pengajuan izin berhasil disetujui.');
    }

    /**
     * Reject pengajuan izin
     */
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

    /**
     * Hapus pengajuan izin
     */
    public function destroy(TherapistLeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }

    /**
     * Dashboard widget - Pending leaves
     */
    public static function getPendingLeaves($limit = 5)
    {
        return TherapistLeaveRequest::pending()
            ->with('therapist')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Dashboard widget - Leaves summary
     */
    public static function getLeavesSummary()
    {
        return [
            'pending'   => TherapistLeaveRequest::pending()->count(),
            'approved'  => TherapistLeaveRequest::approved()->count(),
            'rejected'  => TherapistLeaveRequest::rejected()->count(),
            'total'     => TherapistLeaveRequest::count(),
        ];
    }

    /**
     * API - Get therapist active leaves
     */
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
