<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Promo;
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

        // Ambil / buat customer berdasarkan nomor HP
        $customer = Customer::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name'  => $validated['name']]
        );

        // Jika nama sudah ada tapi mau diupdate
        if ($customer->name !== $validated['name']) {
            $customer->update(['name' => $validated['name']]);
        }

        $service    = Service::findOrFail($validated['service_id']);
        $therapistId = $validated['therapist_id'] ?? null;

        // Jika terapis tidak dipilih, assign otomatis (terapis aktif pertama yang available)
        if (!$therapistId) {
            $therapist = Therapist::where('is_active', 1)->first();
            $therapistId = $therapist?->id;
        }

        Booking::create([
            'customer_id'           => $customer->id,
            'therapist_id'          => $therapistId,
            'service_id'            => $service->id,
            'scheduled_at'          => $validated['scheduled_at'],
            'original_scheduled_at' => $validated['scheduled_at'],
            'is_rescheduled'        => false,
            'order_source'          => 'web',
            'discount'              => 0,
            'final_price'           => $service->price,
            'promo_id'              => null,
            'notes'                 => $validated['notes'] ?? null,
            'status'                => 'scheduled',
        ]);

        return redirect()->back()->with('booking_success', true);
    }
}
