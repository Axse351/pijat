<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    /**
     * Store a guest booking — creates a Customer on-the-fly if not found.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'service_id'   => 'required|exists:services,id',
            'therapist_id' => 'nullable|exists:therapists,id',
            'scheduled_at' => 'required|date|after:now',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Cek konflik hanya jika terapis dipilih
        if (!empty($validated['therapist_id'])) {
            $service  = \App\Models\Service::find($validated['service_id']);
            $duration = $service->duration_minutes ?? 60;
            $start    = Carbon::parse($validated['scheduled_at']);
            $end      = $start->copy()->addMinutes($duration);

            $conflict = \App\Models\Booking::where('therapist_id', $validated['therapist_id'])
                ->whereIn('status', ['scheduled', 'ongoing'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereRaw(
                        'DATE_ADD(scheduled_at, INTERVAL COALESCE(
                        (SELECT duration_minutes FROM services WHERE id = bookings.service_id), 60
                    ) MINUTE) > ?',
                        [$start]
                    )->where('scheduled_at', '<', $end);
                })
                ->exists();

            if ($conflict) {
                return back()->withInput()->withErrors([
                    'scheduled_at' => 'Maaf, terapis ini sudah dipesan pada jam tersebut. Silakan pilih jam lain.'
                ]);
            }
        }

        // ... lanjut simpan seperti biasa
    }
}
