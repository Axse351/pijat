<x-app-layout>
<x-slot name="header">
<h2 class="text-xl font-semibold">
History Membership — {{ $customer->user->name }}
</h2>
</x-slot>

<div class="py-6">
<div class="max-w-5xl mx-auto bg-white rounded-xl border overflow-hidden">

<table class="w-full text-sm">
<thead class="bg-gray-50">
<tr>
<th class="px-5 py-3 text-left text-xs font-semibold uppercase">Membership</th>
<th class="px-5 py-3 text-left text-xs font-semibold uppercase">Mulai</th>
<th class="px-5 py-3 text-left text-xs font-semibold uppercase">Berakhir</th>
<th class="px-5 py-3 text-left text-xs font-semibold uppercase">Status</th>
</tr>
</thead>
<tbody class="divide-y">
@foreach($customer->memberships as $item)
<tr>
<td class="px-5 py-3 font-medium">
{{ $item->membership->name }}
</td>
<td class="px-5 py-3">
{{ $item->start_date }}
</td>
<td class="px-5 py-3">
{{ $item->end_date }}
</td>
<td class="px-5 py-3">
<span class="px-2 py-1 rounded-full text-xs font-semibold
{{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
</span>
</td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>
</x-app-layout>
