<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\WaMessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['user', 'bookings' => function ($q) {
            $q->where('status', 'completed')->orderByDesc('scheduled_at');
        }])->latest()->get();

        $customers->each(function ($c) {
            $completed      = $c->bookings;
            $c->visit_count = $completed->count();
            $c->last_visit  = $completed->first()?->scheduled_at;
            $c->is_inactive = $c->last_visit
                && Carbon::parse($c->last_visit)->lt(now()->subDays(30));
        });

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:users,email|required_without:phone',
            'phone'       => 'nullable|string|max:20|unique:users,phone|required_without:email',
            'password'    => 'required|string|min:8|confirmed',
            'ulang_tahun' => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $phone = $request->phone ? WaMessageTemplate::normalizePhone($request->phone) : null;

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $phone,
                'password' => Hash::make($request->password),
                'role'     => 'user',
            ]);

            Customer::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'email'       => $request->email,
                'phone'       => $phone,
                'ulang_tahun' => $request->ulang_tahun,
                'notes'       => $request->notes,
            ]);
        });

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $customer->user_id . '|required_without:phone',
            'phone'       => 'nullable|string|max:20|unique:users,phone,' . $customer->user_id . '|required_without:email',
            'password'    => 'nullable|string|min:8|confirmed',
            'ulang_tahun' => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $phone = $request->phone ? WaMessageTemplate::normalizePhone($request->phone) : null;

            $userData = ['name' => $request->name, 'email' => $request->email, 'phone' => $phone];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $customer->user->update($userData);

            $customer->update([
                'name'        => $request->name,
                'email'       => $request->email,
                'phone'       => $phone,
                'notes'       => $request->notes,
                'ulang_tahun' => $request->ulang_tahun,
            ]);
        });

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }



    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            $customer->user->delete();
        });

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    // ✅ Klaim bonus — reset poin ke 0
    public function redeemBonus(Customer $customer)
    {
        if (!$customer->hasBonus()) {
            return back()->with('error', 'Poin belum cukup untuk klaim bonus (minimal 10 poin).');
        }

        $customer->redeemBonus();

        return back()->with(
            'success',
            "🎁 Bonus berhasil diklaim untuk {$customer->name}! " .
                "Pelanggan mendapatkan 1x layanan gratis 1 jam. Poin direset ke 0."
        );
    }
}
