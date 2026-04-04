<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Therapist;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $services   = Service::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $therapistSchedules = $this->buildTherapistSchedules($therapists);

        return view('welcome', compact('services', 'therapists', 'therapistSchedules'));
    }

    // ── Endpoint AJAX: slot yang sudah dipesan per terapis per tanggal ──
    public function bookedSlots(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'date'         => 'required|date',
        ]);

        $bookings = Booking::where('therapist_id', $request->therapist_id)
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereDate('scheduled_at', $request->date)
            ->with('service')
            ->get()
            ->map(fn($b) => [
                'start'    => Carbon::parse($b->scheduled_at)->format('H:i'),
                'end'      => Carbon::parse($b->scheduled_at)
                    ->addMinutes($b->service->duration_minutes ?? 60)
                    ->format('H:i'),
                'duration' => $b->service->duration_minutes ?? 60,
            ]);

        return response()->json(['booked' => $bookings]);
    }

    // ── Endpoint AJAX: booking per tanggal untuk tampilan kalender ──
    public function bookingsByDate(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'date'         => 'required|date',
        ]);

        $bookings = Booking::where('therapist_id', $request->therapist_id)
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereDate('scheduled_at', $request->date)
            ->with('service')
            ->get()
            ->map(fn($b) => [
                'time'     => Carbon::parse($b->scheduled_at)->format('H:i'),
                'end_time' => Carbon::parse($b->scheduled_at)
                    ->addMinutes($b->service->duration_minutes ?? 60)
                    ->format('H:i'),
                'service'  => $b->service->name ?? '—',
                'duration' => $b->service->duration_minutes ?? 60,
            ]);

        return response()->json(['bookings' => $bookings]);
    }

    private function buildTherapistSchedules($therapists): array
    {
        $result = [];

        $from = Carbon::now()->startOfMonth()->subMonth();
        $to   = Carbon::now()->endOfMonth()->addMonths(2);

        $allSchedules = \App\Models\TherapistSchedule::query()
            ->whereBetween('schedule_date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->groupBy('therapist_id');

        // Ambil semua booking dalam rentang, group per "therapist_id|tanggal"
        $allBookings = Booking::whereIn('status', ['scheduled', 'ongoing'])
            ->whereBetween('scheduled_at', [$from->startOfDay(), $to->endOfDay()])
            ->with('service')
            ->get()
            ->groupBy(
                fn($b) =>
                $b->therapist_id . '|' . Carbon::parse($b->scheduled_at)->toDateString()
            );

        foreach ($therapists as $t) {
            $firstWorking = $allSchedules->get($t->id)?->firstWhere('status', 'working');

            $schedMap = [];
            foreach ($allSchedules->get($t->id, collect()) as $row) {
                $dateKey = Carbon::parse($row->schedule_date)->toDateString();
                $schedMap[$dateKey] = $row->status;
            }

            // Bangun booking_map: { "2026-04-04": ["09:00","11:00"], ... }
            $bookingMap = [];
            foreach ($allBookings as $key => $bookings) {
                [$tid, $dateStr] = explode('|', $key);
                if ((int)$tid !== $t->id) continue;
                $bookingMap[$dateStr] = $bookings
                    ->map(fn($b) => Carbon::parse($b->scheduled_at)->format('H:i'))
                    ->sort()->values()->toArray();
            }

            $result[(string) $t->id] = [
                'name'        => $t->name,
                'spec'        => $t->specialization ?? 'Terapis Profesional',
                'start_time'  => $firstWorking?->start_time
                    ? Carbon::parse($firstWorking->start_time)->format('H:i') : '09:00',
                'end_time'    => $firstWorking?->end_time
                    ? Carbon::parse($firstWorking->end_time)->format('H:i') : '20:00',
                'schedules'   => $schedMap,
                'booking_map' => $bookingMap,
            ];
        }

        return $result;
    }
}
