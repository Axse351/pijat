<x-app-layout>
<x-slot name="header">
<div class="flex justify-between">
<h2 class="text-xl font-semibold">
Edit Membership — {{ $membership->name }}
</h2>
<a href="{{ route('admin.memberships.index') }}"
class="px-4 py-2 bg-gray-200 text-sm rounded-lg">← Kembali</a>
</div>
</x-slot>

<div class="py-6">
<div class="max-w-2xl mx-auto px-4">
<div class="bg-white rounded-xl border p-6">

<form method="POST" action="{{ route('admin.memberships.update', $membership) }}">
@csrf
@method('PUT')

<div class="mb-5">
<label class="block text-xs font-semibold mb-2">Nama *</label>
<input type="text" name="name"
value="{{ old('name',$membership->name) }}"
class="w-full px-4 py-2 border rounded-lg">
</div>

<div class="mb-5">
<label class="block text-xs font-semibold mb-2">Durasi (Hari) *</label>
<input type="number" name="duration_days"
value="{{ old('duration_days',$membership->duration_days) }}"
class="w-full px-4 py-2 border rounded-lg">
</div>

<button class="px-5 py-2 bg-indigo-600 text-white rounded-lg">
Update
</button>

</form>
</div>
</div>
</div>
</x-app-layout>
