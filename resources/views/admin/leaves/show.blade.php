<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leaves.index') }}"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                ← Kembali
            </a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Detail Pengajuan Izin
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if (session('success'))
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                    <span class="text-emerald-600">✅</span>
                    <span class="text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div
                    class="flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                    <span class="text-red-500">⚠️</span>
                    <div>
                        @foreach ($errors->all() as $e)
                            <p class="text-red-700 dark:text-red-300 text-sm">{{ $e }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Status Banner --}}
            @switch($leaveRequest->status)
                @case('pending')
                    <div
                        class="flex items-center gap-3 px-5 py-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                        <span class="text-2xl">⏳</span>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-300">Menunggu Persetujuan Admin</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">Pengajuan ini belum diproses. Silakan tinjau
                                dan berikan keputusan.</p>
                        </div>
                    </div>
                @break

                @case('approved')
                    <div
                        class="flex items-center gap-3 px-5 py-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-300 dark:border-emerald-700 rounded-lg">
                        <span class="text-2xl">✅</span>
                        <div>
                            <p class="font-semibold text-emerald-800 dark:text-emerald-300">Pengajuan Telah Disetujui</p>
                            @if ($leaveRequest->approved_at)
                                <p class="text-sm text-emerald-700 dark:text-emerald-400">
                                    Diproses pada {{ $leaveRequest->approved_at->format('d M Y, H:i') }}
                                    @if ($leaveRequest->approver)
                                        oleh {{ $leaveRequest->approver->name }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @break

                @case('rejected')
                    <div
                        class="flex items-center gap-3 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg">
                        <span class="text-2xl">❌</span>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-300">Pengajuan Ditolak</p>
                            @if ($leaveRequest->approved_at)
                                <p class="text-sm text-red-700 dark:text-red-400">
                                    Diproses pada {{ $leaveRequest->approved_at->format('d M Y, H:i') }}
                                    @if ($leaveRequest->approver)
                                        oleh {{ $leaveRequest->approver->name }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @break

            @endswitch

            {{-- Main Card --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                {{-- Therapist Header --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-4">
                        @if ($leaveRequest->therapist->photo)
                            <img src="{{ Storage::url($leaveRequest->therapist->photo) }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shadow"
                                alt="">
                        @else
                            <div
                                class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xl ring-2 ring-white dark:ring-gray-700 shadow">
                                {{ strtoupper(substr($leaveRequest->therapist->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $leaveRequest->therapist->name }}</h3>
                            @if ($leaveRequest->therapist->specialty)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $leaveRequest->therapist->specialty }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">Diajukan:
                                {{ $leaveRequest->created_at->diffForHumans() }}
                                ({{ $leaveRequest->created_at->format('d M Y, H:i') }})</p>
                        </div>
                    </div>
                </div>

                {{-- Detail Grid --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jenis Izin</p>
                        @switch($leaveRequest->type)
                            @case('sakit')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium">🏥
                                    Sakit</span>
                            @break

                            @case('pribadi')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg font-medium">👤
                                    Keperluan Pribadi</span>
                            @break

                            @case('cuti')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 rounded-lg font-medium">🏖️
                                    Cuti</span>
                            @break

                            @case('izin_khusus')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg font-medium">⭐
                                    Izin Khusus</span>
                            @break
                        @endswitch
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Durasi</p>
                        <span
                            class="inline-block px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg font-semibold">
                            📅
                            {{ $leaveRequest->day_count ?? $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }}
                            hari
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Mulai</p>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ $leaveRequest->start_date->format('l, d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Selesai</p>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ $leaveRequest->end_date->format('l, d M Y') }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alasan</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                {{ $leaveRequest->reason }}</p>
                        </div>
                    </div>

                    {{-- Approval Notes --}}
                    @if ($leaveRequest->approval_notes)
                        <div class="md:col-span-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                {{ $leaveRequest->status === 'rejected' ? 'Alasan Penolakan' : 'Catatan Admin' }}
                            </p>
                            <div
                                class="p-4 {{ $leaveRequest->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800' }} rounded-lg">
                                <p
                                    class="{{ $leaveRequest->status === 'rejected' ? 'text-red-700 dark:text-red-300' : 'text-emerald-700 dark:text-emerald-300' }} text-sm leading-relaxed">
                                    {{ $leaveRequest->approval_notes }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                @if ($leaveRequest->status === 'pending')
                    <div class="px-6 py-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">⚡ Keputusan Admin</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Form Approve --}}
                            <form action="{{ route('admin.leaves.approve', $leaveRequest) }}" method="POST"
                                class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg space-y-3">
                                @csrf @method('PATCH')
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">✅</span>
                                    <h4 class="font-semibold text-emerald-800 dark:text-emerald-300">Setujui Izin</h4>
                                </div>
                                <div>
                                    <label class="block text-xs text-emerald-700 dark:text-emerald-400 mb-1">Catatan
                                        (opsional)</label>
                                    <textarea name="approval_notes" rows="2" placeholder="Catatan untuk terapis..."
                                        class="w-full px-3 py-2 text-sm border border-emerald-300 dark:border-emerald-700 rounded-lg dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition">
                                    ✅ Setujui Pengajuan Ini
                                </button>
                            </form>

                            {{-- Form Reject --}}
                            <form action="{{ route('admin.leaves.reject', $leaveRequest) }}" method="POST"
                                class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg space-y-3">
                                @csrf @method('PATCH')
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">❌</span>
                                    <h4 class="font-semibold text-red-800 dark:text-red-300">Tolak Izin</h4>
                                </div>
                                <div>
                                    <label class="block text-xs text-red-700 dark:text-red-400 mb-1">Alasan Penolakan
                                        <span class="font-bold">*</span></label>
                                    <textarea name="approval_notes" rows="2" required placeholder="Jelaskan alasan penolakan (min. 10 karakter)..."
                                        class="w-full px-3 py-2 text-sm border border-red-300 dark:border-red-700 rounded-lg dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition">
                                    ❌ Tolak Pengajuan Ini
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Link ke Jadwal Terapis --}}
            <div class="text-center">
                <a href="{{ route('admin.schedules.index', ['therapist_id' => $leaveRequest->therapist_id, 'month' => $leaveRequest->start_date->month, 'year' => $leaveRequest->start_date->year]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    🗓 Lihat Jadwal Bulan {{ $leaveRequest->start_date->format('F Y') }} →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
