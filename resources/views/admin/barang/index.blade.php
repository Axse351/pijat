<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Manajemen Inventaris</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Daftar Barang
                </h2>
            </div>
            <a href="{{ route('admin.barang.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Tambah Barang
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total Barang</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $summary['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Aktif</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $summary['aktif'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stok Habis</p>
                    <p class="text-2xl font-bold text-red-500 mt-1">{{ $summary['habis'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Hampir Habis</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ $summary['hampir_habis'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Ada Selisih</p>
                    <p class="text-2xl font-bold text-orange-500 mt-1">{{ $summary['ada_selisih'] }}</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <form method="GET" action="{{ route('admin.barang.index') }}" class="flex flex-wrap gap-3">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[180px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama / kode barang…"
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    {{-- Kategori --}}
                    <select name="kategori"
                        class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Kondisi Stok --}}
                    <select name="kondisi"
                        class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Kondisi</option>
                        <option value="habis" {{ request('kondisi') === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                        <option value="hampir_habis" {{ request('kondisi') === 'hampir_habis' ? 'selected' : '' }}>Hampir Habis</option>
                        <option value="selisih" {{ request('kondisi') === 'selisih' ? 'selected' : '' }}>Ada Selisih</option>
                    </select>

                    {{-- Status --}}
                    <select name="status"
                        class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'kategori', 'kondisi', 'status']))
                        <a href="{{ route('admin.barang.index') }}"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel Barang --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if ($barangs->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Sistem</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Aktual</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Selisih</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kroscek Terakhir</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($barangs as $barang)
                                    @php
                                        $stokSistem = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar;
                                        $selisih = $barang->stok_aktual - $stokSistem;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">
                                            {{ $barang->kode_barang }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <a href="{{ route('admin.barang.show', $barang) }}"
                                                class="font-semibold text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $barang->nama_barang }}
                                            </a>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $barang->satuan }} · {{ $barang->lokasi_simpan ?? '—' }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $barang->kategori }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if ($stokSistem <= 0)
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">{{ $stokSistem }}</span>
                                            @elseif ($stokSistem <= $barang->stok_minimum)
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">{{ $stokSistem }}</span>
                                            @else
                                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $stokSistem }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-medium text-gray-700 dark:text-gray-300">
                                            {{ $barang->stok_aktual }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if ($selisih != 0)
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $selisih > 0 ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $selisih > 0 ? '+' : '' }}{{ $selisih }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-xs">
                                            @if ($barang->tanggal_kroscek)
                                                <p>{{ \Carbon\Carbon::parse($barang->tanggal_kroscek)->format('d M Y') }}</p>
                                                <p class="text-gray-400">{{ $barang->petugas_kroscek }}</p>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">Belum pernah</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $barang->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ ucfirst($barang->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ route('admin.barang.show', $barang) }}"
                                                    class="px-2.5 py-1 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-lg">
                                                    Detail
                                                </a>
                                                <a href="{{ route('admin.barang.edit', $barang) }}"
                                                    class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">
                                                    Edit
                                                </a>
                                                {{-- Kroscek mini form --}}
                                                <button type="button"
                                                    onclick="openKroscek({{ $barang->id }}, '{{ $barang->nama_barang }}', {{ $barang->stok_aktual }})"
                                                    class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium rounded-lg">
                                                    Kroscek
                                                </button>
                                                <form method="POST" action="{{ route('admin.barang.destroy', $barang) }}"
                                                    onsubmit="return confirm('Hapus barang ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $barangs->links() }}
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">
                        <p class="text-3xl mb-2">📦</p>
                        <p>Tidak ada barang ditemukan.</p>
                        @if (request()->hasAny(['search', 'kategori', 'kondisi', 'status']))
                            <a href="{{ route('admin.barang.index') }}" class="mt-2 inline-block text-indigo-500 hover:underline">Hapus filter</a>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal Kroscek --}}
    <div id="modalKroscek"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200" id="kroscekTitle">Kroscek Stok</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="kroscekSubtitle"></p>
                </div>
                <button onclick="closeKroscek()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl leading-none">&times;</button>
            </div>

            <form id="formKroscek" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Stok Aktual</label>
                        <input type="number" name="stok_aktual" id="kroscekStok" min="0" required
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Petugas</label>
                        <input type="text" name="petugas_kroscek" required
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            placeholder="Nama petugas…">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan</label>
                        <textarea name="catatan" rows="2"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            placeholder="Opsional…"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" onclick="closeKroscek()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan Kroscek
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openKroscek(id, nama, stokAktual) {
            document.getElementById('kroscekTitle').textContent = 'Kroscek: ' + nama;
            document.getElementById('kroscekSubtitle').textContent = 'Stok aktual saat ini: ' + stokAktual;
            document.getElementById('kroscekStok').value = stokAktual;
            document.getElementById('formKroscek').action = '/admin/barang/' + id + '/kroscek';
            const modal = document.getElementById('modalKroscek');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeKroscek() {
            const modal = document.getElementById('modalKroscek');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('modalKroscek').addEventListener('click', function (e) {
            if (e.target === this) closeKroscek();
        });
    </script>
    @endpush

</x-app-layout>
