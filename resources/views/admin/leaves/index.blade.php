<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 Pengajuan Izin Terapis
            </h2>
            @if ($summary['pending'] > 0)
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                    ⏳ {{ $summary['pending'] }} menunggu persetujuan
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 dark:text-red-300 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $summaryCards = [
                        ['label' => 'Menunggu', 'count' => $summary['pending'], 'icon' => '⏳'],
                        ['label' => 'Disetujui', 'count' => $summary['approved'], 'icon' => '✅'],
                        ['label' => 'Ditolak', 'count' => $summary['rejected'], 'icon' => '❌'],
                        ['label' => 'Total', 'count' => $summary['total'], 'icon' => '📋'],
                    ];
                @endphp
                @foreach ($summaryCards as $s)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                        <div class="text-2xl mb-1">{{ $s['icon'] }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['count'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳
                                Menunggu</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✅
                                Disetujui</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌
                                Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Terapis</label>
                        <select name="therapist_id" onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Terapis</option>
                            @foreach ($therapists as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('therapist_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
                        <select name="month" onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    @if (request()->hasAny(['status', 'therapist_id', 'month']))
                        <a href="{{ route('admin.leaves.index') }}"
                            class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg transition">
                            ✕ Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                @if ($leaves->isEmpty())
                    <div class="py-16 text-center text-gray-400 dark:text-gray-500">
                        <div class="text-5xl mb-4">📭</div>
                        <p class="text-lg font-medium">Tidak ada data pengajuan izin</p>
                        <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700/60 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left">Terapis</th>
                                    <th class="px-4 py-3 text-left">Jenis Izin</th>
                                    <th class="px-4 py-3 text-left">Periode</th>
                                    <th class="px-4 py-3 text-center">Durasi</th>
                                    <th class="px-4 py-3 text-left">Alasan</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($leaves as $leave)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition {{ $leave->status === 'pending' ? 'bg-yellow-50/40 dark:bg-yellow-900/10' : '' }}">

                                        {{-- Terapis --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                @if ($leave->therapist->photo)
                                                    <img src="{{ Storage::url($leave->therapist->photo) }}"
                                                        class="w-8 h-8 rounded-full object-cover" alt="">
                                                @else
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-xs">
                                                        {{ strtoupper(substr($leave->therapist->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">
                                                        {{ $leave->therapist->name }}</p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ $leave->created_at->format('d M Y') }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Jenis --}}
                                        <td class="px-4 py-3">
                                            @switch($leave->type)
                                                @case('sakit')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-medium">🏥
                                                        Sakit</span>
                                                @break

                                                @case('pribadi')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-medium">👤
                                                        Pribadi</span>
                                                @break

                                                @case('cuti')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 rounded text-xs font-medium">🏖️
                                                        Cuti</span>
                                                @break

                                                @case('izin_khusus')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded text-xs font-medium">⭐
                                                        Khusus</span>
                                                @break

                                                @default
                                                    <span
                                                        class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $leave->type }}</span>
                                            @endswitch
                                        </td>

                                        {{-- Periode --}}
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                            <p class="font-medium">{{ $leave->start_date->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-400">s/d
                                                {{ $leave->end_date->format('d M Y') }}</p>
                                        </td>

                                        {{-- Durasi --}}
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-block px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded font-semibold text-xs">
                                                {{ $leave->day_count }} hari
                                            </span>
                                        </td>

                                        {{-- Alasan --}}
                                        <td class="px-4 py-3 max-w-xs">
                                            <p class="text-gray-600 dark:text-gray-400 text-xs line-clamp-2">
                                                {{ $leave->reason }}</p>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3 text-center">
                                            @switch($leave->status)
                                                @case('pending')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 rounded-full text-xs font-semibold">
                                                        ⏳ Menunggu
                                                    </span>
                                                @break

                                                @case('approved')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full text-xs font-semibold">
                                                        ✅ Disetujui
                                                    </span>
                                                @break

                                                @case('rejected')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 rounded-full text-xs font-semibold">
                                                        ❌ Ditolak
                                                    </span>
                                                @break
                                            @endswitch
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.leaves.show', $leave) }}"
                                                    class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded transition"
                                                    title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if ($leave->status === 'pending')
                                                    <button type="button"
                                                        onclick="openApproveModal({{ $leave->id }}, '{{ $leave->therapist->name }}', '{{ $leave->start_date->format('d M Y') }}', '{{ $leave->end_date->format('d M Y') }}')"
                                                        class="p-1.5 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded transition"
                                                        title="Setujui">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>

                                                    <button type="button"
                                                        onclick="openRejectModal({{ $leave->id }}, '{{ $leave->therapist->name }}', '{{ $leave->start_date->format('d M Y') }}', '{{ $leave->end_date->format('d M Y') }}')"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded transition"
                                                        title="Tolak">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                <form action="{{ route('admin.leaves.destroy', $leave) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin hapus pengajuan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded transition"
                                                        title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($leaves->hasPages())
                        <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $leaves->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    {{-- ═══════════════ MODAL APPROVE ═══════════════ --}}
    <div id="approveModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        onclick="if(event.target===this) closeApproveModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 text-xl">
                        ✅</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Setujui Pengajuan Izin</h3>
                        <p id="approveInfo" class="text-sm text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catatan Persetujuan <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <textarea name="approval_notes" rows="3" placeholder="Tambahkan catatan untuk terapis..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeApproveModal()"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                            ✅ Setujui Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL REJECT ═══════════════ --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        onclick="if(event.target===this) closeRejectModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 text-xl">
                        ❌</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tolak Pengajuan Izin</h3>
                        <p id="rejectInfo" class="text-sm text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="approval_notes" rows="3" required
                            placeholder="Jelaskan alasan penolakan kepada terapis (minimal 10 karakter)..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                        <p class="text-xs text-gray-400 mt-1">Wajib diisi minimal 10 karakter</p>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            ❌ Tolak Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(id, name, start, end) {
            document.getElementById('approveInfo').textContent = name + ' · ' + start + ' – ' + end;
            document.getElementById('approveForm').action = '/admin/leaves/' + id + '/approve';
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(id, name, start, end) {
            document.getElementById('rejectInfo').textContent = name + ' · ' + start + ' – ' + end;
            document.getElementById('rejectForm').action = '/admin/leaves/' + id + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
