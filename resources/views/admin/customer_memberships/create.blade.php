<x-app-layout>
<x-slot name="header">
<h2 class="text-xl font-semibold">
Assign Membership — {{ $customer->user->name }}
</h2>
</x-slot>

<div class="py-6">
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl border">

<form method="POST" action="{{ route('admin.customers.membership.store') }}">
@csrf

<input type="hidden" name="customer_id" value="{{ $customer->id }}">

<div class="mb-5">
<label class="block text-xs font-semibold mb-2">Pilih Membership</label>
<select name="membership_id"
class="w-full px-4 py-2 border rounded-lg">
@foreach($memberships as $membership)
<option value="{{ $membership->id }}">
{{ $membership->name }} ({{ $membership->duration_days }} Hari)
</option>
@endforeach
</select>
</div>

<button class="px-5 py-2 bg-indigo-600 text-white rounded-lg">
Assign
</button>

</form>
</div>
</div>
</x-app-layout>
