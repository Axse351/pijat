<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\CustomerMembership;
use Carbon\Carbon;

class CustomerMembershipController extends Controller
{
    public function create($customer_id)
    {
        $customer = Customer::with('user')->findOrFail($customer_id);
        $memberships = Membership::all();

        return view('admin.customer_memberships.create', compact('customer', 'memberships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'membership_id' => 'required|exists:memberships,id'
        ]);

        $membership = Membership::findOrFail($request->membership_id);

        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($membership->duration_days);

        // Nonaktifkan membership lama
        CustomerMembership::where('customer_id', $request->customer_id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Buat membership baru
        CustomerMembership::create([
            'customer_id' => $request->customer_id,
            'membership_id' => $membership->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Membership berhasil ditambahkan');
    }

    public function history($customer_id)
    {
        $customer = Customer::with(['user','memberships.membership'])
            ->findOrFail($customer_id);

        return view('admin.customer_memberships.history', compact('customer'));
    }

    public function edit($id)
{
    $customerMembership = CustomerMembership::findOrFail($id);
    $memberships = Membership::all();

    return view('admin.customer_memberships.edit',
        compact('customerMembership','memberships'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'membership_id' => 'required|exists:memberships,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'is_active' => 'required|boolean'
    ]);

    $membership = CustomerMembership::findOrFail($id);
    $membership->update($request->all());

    return redirect()
        ->route('admin.customers.membership.history', $membership->customer_id)
        ->with('success','Membership berhasil diupdate');
}
}
