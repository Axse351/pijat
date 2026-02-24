<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('user')->latest()->get();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'ulang_tahun' => 'nullable|date',
            'notes'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat User terlebih dahulu
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'phone'    => $request->phone,
                'ulang_tahun' => $request->ulang_tahun,
                'role'     => 'user',
            ]);

            // 2. Buat Customer yang terhubung ke User
            Customer::create([
                'user_id' => $user->id,
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'ulang_tahun' => $request->ulang_tahun,
                'notes'   => $request->notes,
            ]);
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $customer->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'ulang_tahun' => 'nullable|date',
            'notes'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $customer) {
            // Update User
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $customer->user->update($userData);

            // Update Customer
            $customer->update([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'ulang_tahun' => $request->ulang_tahun,
            ]);
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            // Hapus user juga (cascade akan hapus customer otomatis)
            $customer->user->delete();
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
