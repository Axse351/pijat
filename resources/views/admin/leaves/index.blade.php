<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pengajuan Izin Terapis
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    ✕ {{ session('error') }}
                </div>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                @php
                    $summary = App\Http\Controllers\Admin\TherapistLeaveController::getLeavesSummary();
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $summary['pending'] }}</div>
                    <div class="text-sm text-gray-500 mt-2">Menunggu Approval</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl font-bold text-emerald-600">{{ $summary['approved'] }}</div>
                    <div class="text-sm text-gray-500 mt-2">Sudah Disetujui</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl font-bold text-red-600">{{ $summary['rejected'] }}</div>
                    <div class="text-sm text-gray-500 mt-2">Ditolak</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl font-bold text-blue-600">{{ $summary['total'] }}</div>
                    <div class="text-sm text-gray-500 mt-2">Total</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="mb-6 flex gap-2 flex-wrap">
                <form method="GET" class="flex gap-2">
                    <button type="submit" name="status" value=""
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        Semua
                    </button>
                    <button type="submit" name="status" value="pending"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ⏳ Menunggu
                    </button>
                    <button type="submit" name="status" value="approved"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ✅ Disetujui
                    </button>
                    <button type="submit" name="status" value="rejected"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        ❌ Ditolak
                    </button>
                </form>
            </div>

            {{-- Leaves List --}}
            @if ($leaves->count())
                <div class="space-y-3">
                    @foreach ($leaves as $leave)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition {{ $leave->status === 'pending' ? 'border-l-4 border-l-blue-500' : ($leave->status === 'approved' ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-red-500') }}">
                            <div class="p-6">
                                {{-- Header --}}
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-4">
                                        {{-- Avatar --}}
                                        <div
                                            class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                                            {{ strtoupper(substr($leave->therapist->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                {{ $leave->therapist->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $leave->therapist->specialty ?? 'Terapis' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $typeColor = match ($leave->type) {
                                                'sakit' => 'bg-red-100 text-red-700',
                                                'pribadi' => 'bg-blue-100 text-blue-700',
                                                'cuti' => 'bg-green-100 text-green-700',
                                                'izin_khusus' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };

                                            $statusColor = match ($leave->status) {
                                                'pending' => 'bg-blue-100 text-blue-700',
                                                'approved' => 'bg-emerald-100 text-emerald-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $typeColor }}">
                                            {{ TherapistLeaveRequest::getTypeLabel($leave->type) }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                            {{ TherapistLeaveRequest::getStatusLabel($leave->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Details Grid --}}
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
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
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold">
                                            Diajukan</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            {{ $leave->created_at->diffForHumans() }}
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
                                    <a href="{{ route('admin.leaves.show', $leave) }}"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                        👁️ Detail
                                    </a>

                                    @if ($leave->status === 'pending')
                                        <button type="button" onclick="openApproveModal({{ $leave->id }})"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                            ✓ Setujui
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $leave->id }})"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                            ✕ Tolak
                                        </button>
                                    @else
                                        <form action="{{ route('admin.leaves.destroy', $leave) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                                🗑️ Hapus
                                            </button>
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
                    <div class="text-4xl mb-4">✅</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-2">
                        Semua Pengajuan Sudah Diproses
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Tidak ada pengajuan izin yang menunggu persetujuan Anda.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal untuk Approve --}}
    <div id="approveModal" style="display:none;"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Setujui Pengajuan Izin</h3>
            </div>
            <form id="approveForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="approval_notes"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                        rows="4" placeholder="Tambahkan catatan atau keterangan..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                        ✓ Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal untuk Reject --}}
    <div id="rejectModal" style="display:none;"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tolak Pengajuan Izin</h3>
            </div>
            <form id="rejectForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alasan Penolakan <span class="text-red-600">*</span>
                    </label>
                    <textarea name="approval_notes" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                        rows="4" placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        ✕ Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(leaveId) {
            document.getElementById('approveForm').action = `/admin/leaves/${leaveId}/approve`;
            document.getElementById('approveModal').style.display = 'flex';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
        }

        function openRejectModal(leaveId) {
            document.getElementById('rejectForm').action = `/admin/leaves/${leaveId}/reject`;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (e.target === approveModal) closeApproveModal();
            if (e.target === rejectModal) closeRejectModal();
        });

        // Close modal with Escape key
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
