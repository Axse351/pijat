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
    public function index(Customer $customer)
    {
        $customer->load('user');

        $activeMembership = CustomerMembership::with('membership')
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->first();

        $histories = CustomerMembership::with('membership')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('admin.customer_memberships.index', compact('customer', 'activeMembership', 'histories'));
    }

    public function create(Customer $customer)
    {
        $customer->load('user');
        $memberships = Membership::orderBy('name')->get();

        return view('admin.customer_memberships.create', compact('customer', 'memberships'));
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);

        CustomerMembership::where('customer_id', $customer->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        CustomerMembership::create([
            'customer_id'   => $customer->id,
            'membership_id' => $membership->id,
            'start_date'    => Carbon::today(),
            'end_date'      => Carbon::today()->addDays($membership->duration_days),
            'is_active'     => true,
        ]);

        return redirect()
            ->route('admin.customers.membership.index', $customer)
            ->with('success', "Membership {$membership->name} berhasil diberikan.")
            ->with('welcome_membership', [
                'customer_name'   => $customer->user->name,
                'membership_name' => $membership->name,
                'end_date'        => Carbon::today()->addDays($membership->duration_days)->translatedFormat('d F Y'),
                'phone'           => $customer->phone ?? $customer->user->phone ?? null, // ← tambah baris ini saja
            ]);
    }

    public function edit(Customer $customer, CustomerMembership $customerMembership)
    {
        $customer->load('user');
        $memberships = Membership::orderBy('name')->get();

        return view('admin.customer_memberships.edit', compact('customer', 'customerMembership', 'memberships'));
    }

    public function update(Request $request, Customer $customer, CustomerMembership $customerMembership)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'start_date'    => 'required|date',
            'is_active'     => 'boolean',
        ]);

        $membership = Membership::findOrFail($request->membership_id);

        $customerMembership->update([
            'membership_id' => $membership->id,
            'start_date'    => $request->start_date,
            'end_date'      => Carbon::parse($request->start_date)->addDays($membership->duration_days),
            'is_active'     => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.customers.membership.index', $customer)
            ->with('success', 'Membership berhasil diperbarui.');
    }

    public function destroy(Customer $customer, CustomerMembership $customerMembership)
    {
        $customerMembership->delete();

        return redirect()
            ->route('admin.customers.membership.index', $customer)
            ->with('success', 'Membership berhasil dihapus.');
    }
}
