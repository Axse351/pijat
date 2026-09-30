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
        $memberships = Membership::orderBy('name')->get();

        return view('admin.customer_memberships.create', compact('customer', 'memberships'));
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $endDate    = Carbon::today()->addDays($membership->duration_days);

        // Nonaktifkan membership lama
        CustomerMembership::where('customer_id', $customer->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        CustomerMembership::create([
            'customer_id'   => $customer->id,
            'membership_id' => $membership->id,
            'start_date'    => Carbon::today(),
            'end_date'      => $endDate,
            'is_active'     => true,
        ]);

        return redirect()
            ->route('admin.customers.membership.index', $customer)
            ->with('success', "Membership {$membership->name} berhasil diberikan.")
            ->with('welcome_membership', [
                'customer_name'   => $customer->name,
                'membership_name' => $membership->name,
                'end_date'        => $endDate->translatedFormat('d F Y'),
                'phone'           => $customer->phone,
            ]);
    }

    public function edit(Customer $customer, CustomerMembership $customerMembership)
    {
        $memberships = Membership::orderBy('name')->get();

        return view('admin.customer_memberships.edit', compact('customer', 'customerMembership', 'memberships'));
    }

    public function update(Request $request, Customer $customer, CustomerMembership $customerMembership)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'start_date'    => 'required|date',
            'is_active'     => 'nullable|boolean',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $isActive   = $request->boolean('is_active');

        // Hanya boleh ada 1 membership aktif per pelanggan
        if ($isActive) {
            CustomerMembership::where('customer_id', $customer->id)
                ->where('id', '!=', $customerMembership->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $customerMembership->update([
            'membership_id' => $membership->id,
            'start_date'    => $request->start_date,
            'end_date'      => Carbon::parse($request->start_date)->addDays($membership->duration_days),
            'is_active'     => $isActive,
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
