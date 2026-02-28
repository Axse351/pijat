<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Manajemen Inventaris</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit Barang — <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $barang->kode_barang }}</span>
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.barang.show', $barang) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    ← Detail
                </a>
                <a href="{{ route('admin.barang.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    Daftar Barang
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.barang.update', $barang) }}" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Identitas Barang --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Identitas Barang</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Kode Barang --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Kode Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}"
                                class="w-full border rounded-lg px-3 py-2 text-sm font-mono bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('kode_barang') border-red-400 @else border-gray-200 @enderror">
                            @error('kode_barang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Barang --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('nama_barang') border-red-400 @else border-gray-200 @enderror">
                            @error('nama_barang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="kategori"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('kategori') border-red-400 @else border-gray-200 @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori', $barang->kategori) === $kat ? 'selected' : '' }}>
                                        {{ $kat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Satuan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <select name="satuan"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('satuan') border-red-400 @else border-gray-200 @enderror">
                                <option value="">-- Pilih Satuan --</option>
                                @foreach ($satuans as $sat)
                                    <option value="{{ $sat }}" {{ old('satuan', $barang->satuan) === $sat ? 'selected' : '' }}>
                                        {{ $sat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Lokasi Simpan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Lokasi Simpan</label>
                            <input type="text" name="lokasi_simpan" value="{{ old('lokasi_simpan', $barang->lokasi_simpan) }}"
                                placeholder="Rak A1, Gudang B, dll."
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        {{-- Tanggal Kadaluarsa --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_kadaluarsa"
                                value="{{ old('tanggal_kadaluarsa', $barang->tanggal_kadaluarsa?->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        {{-- Status --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="aktif"
                                        {{ old('status', $barang->status) === 'aktif' ? 'checked' : '' }}
                                        class="text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="nonaktif"
                                        {{ old('status', $barang->status) === 'nonaktif' ? 'checked' : '' }}
                                        class="text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Data Stok --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Data Stok</h3>
                        @php
                            $stokSistem = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar;
                            $selisih = $barang->stok_aktual - $stokSistem;
                        @endphp
                        <div class="flex items-center gap-3 text-xs">
                            <span class="text-gray-400">Sistem: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $stokSistem }}</span></span>
                            <span class="text-gray-400">Aktual: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $barang->stok_aktual }}</span></span>
                            @if ($selisih != 0)
                                <span class="px-2 py-0.5 rounded-full font-semibold {{ $selisih > 0 ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                                    Selisih {{ $selisih > 0 ? '+' : '' }}{{ $selisih }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Sesuai</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Stok Awal <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stok_awal" value="{{ old('stok_awal', $barang->stok_awal) }}" min="0"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('stok_awal') border-red-400 @else border-gray-200 @enderror">
                            @error('stok_awal')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Stok Masuk</label>
                            <input type="number" name="stok_masuk" value="{{ old('stok_masuk', $barang->stok_masuk) }}" min="0"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Stok Keluar</label>
                            <input type="number" name="stok_keluar" value="{{ old('stok_keluar', $barang->stok_keluar) }}" min="0"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Stok Aktual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stok_aktual" value="{{ old('stok_aktual', $barang->stok_aktual) }}" min="0"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('stok_aktual') border-red-400 @else border-gray-200 @enderror">
                            @error('stok_aktual')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Stok Minimum <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('stok_minimum') border-red-400 @else border-gray-200 @enderror">
                            @error('stok_minimum')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Harga --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Harga</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" min="0" step="100"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" min="0" step="100"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                    </div>
                </div>

                {{-- Kroscek --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Catatan & Kroscek</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal Kroscek</label>
                            <input type="date" name="tanggal_kroscek"
                                value="{{ old('tanggal_kroscek', $barang->tanggal_kroscek?->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Petugas Kroscek</label>
                            <input type="text" name="petugas_kroscek" value="{{ old('petugas_kroscek', $barang->petugas_kroscek) }}"
                                placeholder="Nama petugas"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan</label>
                            <textarea name="catatan" rows="3"
                                placeholder="Catatan tambahan…"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan', $barang->catatan) }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pb-4">
                    {{-- Hapus --}}
                    <form method="POST" action="{{ route('admin.barang.destroy', $barang) }}"
                        onsubmit="return confirm('Yakin hapus barang ini? Tindakan tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors">
                            Hapus Barang
                        </button>
                    </form>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.barang.show', $barang) }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
