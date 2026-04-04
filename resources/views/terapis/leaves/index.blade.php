<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pengajuan Izin
            </h2>
            <a href="{{ route('terapis.leaves.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Ajukan Izin Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Filter Buttons --}}
            <div class="mb-6 flex gap-2 flex-wrap">
                <form method="GET" class="flex gap-2 flex-wrap">
                    <button type="submit" name="status" value=""
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        Semua ({{ $leaves->total() }})
                    </button>
                    <button type="submit" name="status" value="pending"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ⏳ Menunggu ({{ $therapist->leaveRequests()->pending()->count() }})
                    </button>
                    <button type="submit" name="status" value="approved"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ✅ Disetujui ({{ $therapist->leaveRequests()->approved()->count() }})
                    </button>
                    <button type="submit" name="status" value="rejected"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ❌ Ditolak ({{ $therapist->leaveRequests()->rejected()->count() }})
                    </button>
                </form>
            </div>

            {{-- Leave Requests List --}}
            @if ($leaves->count())
                <div class="space-y-3 mb-6">
                    @foreach ($leaves as $leave)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition {{ $leave->status === 'pending' ? 'border-l-4 border-l-blue-500' : ($leave->status === 'approved' ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-red-500') }}">
                            <div class="p-6">
                                {{-- Type & Status Badges --}}
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        @php
                                            $typeColor = match ($leave->type) {
                                                'sakit' => 'bg-red-100 text-red-700',
                                                'pribadi' => 'bg-blue-100 text-blue-700',
                                                'cuti' => 'bg-green-100 text-green-700',
                                                'izin_khusus' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $typeColor }} mb-3">
                                            {{ TherapistLeaveRequest::getTypeLabel($leave->type) }}
                                        </span>
                                    </div>
                                    @php
                                        $statusColor = match ($leave->status) {
                                            'pending' => 'bg-blue-100 text-blue-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                        {{ TherapistLeaveRequest::getStatusLabel($leave->status) }}
                                    </span>
                                </div>

                                {{-- Date Range --}}
                                <div class="mb-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $leave->start_date->translatedFormat('d F Y') }} -
                                        {{ $leave->end_date->translatedFormat('d F Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $leave->day_count }} hari · Diajukan
                                        {{ $leave->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                {{-- Details Grid --}}
                                <div
                                    class="grid grid-cols-3 gap-4 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Mulai
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            {{ $leave->start_date->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Selesai
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            {{ $leave->end_date->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Durasi
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            {{ $leave->day_count }} Hari
                                        </div>
                                    </div>
                                </div>

                                {{-- Reason --}}
                                <div class="mb-4">
                                    <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2">Alasan
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ $leave->reason }}
                                    </p>
                                </div>

                                {{-- Approval Notes (if not pending) --}}
                                @if ($leave->status !== 'pending' && $leave->approval_notes)
                                    <div
                                        class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded">
                                        <div
                                            class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-400 font-semibold mb-1">
                                            Catatan Admin</div>
                                        <p class="text-sm text-amber-900 dark:text-amber-300 leading-relaxed">
                                            {{ $leave->approval_notes }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex gap-2 flex-wrap">
                                    <a href="{{ route('terapis.leaves.show', $leave) }}"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                        👁️ Detail
                                    </a>

                                    @if ($leave->status === 'pending')
                                        <button type="button"
                                            onclick="if(confirm('Yakin ingin membatalkan pengajuan ini?')) { document.getElementById('cancel-form-{{ $leave->id }}').submit(); }"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                            🗑️ Batalkan
                                        </button>

                                        <form id="cancel-form-{{ $leave->id }}"
                                            action="{{ route('terapis.leaves.destroy', $leave) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $leaves->links() }}
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="text-4xl mb-4">📋</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-2">
                        Belum Ada Pengajuan Izin
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Mulai dengan mengajukan izin/cuti Anda untuk disetujui admin.
                    </p>
                    <a href="{{ route('terapis.leaves.create') }}"
                        class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                        + Ajukan Izin Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
