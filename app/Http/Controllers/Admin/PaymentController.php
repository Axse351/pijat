<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('booking')->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $bookings = Booking::where('status','completed')
                            ->whereDoesntHave('payment')
                            ->get();

        return view('admin.payments.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        Payment::create([
            'booking_id' => $request->booking_id,
            'method' => $request->method,
            'amount' => $request->amount,
            'paid_at' => now()
        ]);

        return redirect()->route('admin.payments.index');
    }
}
