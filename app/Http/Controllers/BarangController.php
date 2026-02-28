<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar semua barang beserta status stok & kroscek.
     */
    public function index(Request $request)
    {
        $query = Barang::query();

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter kondisi stok
        if ($request->filled('kondisi')) {
            match ($request->kondisi) {
                'habis'        => $query->stokHabis(),
                'hampir_habis' => $query->stokRendah(),
                'selisih'      => $query->adaSelisih(),
                default        => null,
            };
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $barangs = $query->orderBy('nama_barang')->paginate(15)->withQueryString();

        // Ringkasan untuk dashboard mini
        $summary = [
            'total'        => Barang::count(),
            'aktif'        => Barang::aktif()->count(),
            'habis'        => Barang::aktif()->stokHabis()->count(),
            'hampir_habis' => Barang::aktif()->stokRendah()->count(),
            'ada_selisih'  => Barang::aktif()->adaSelisih()->count(),
        ];

        $kategoris = Barang::daftarKategori();

        return view('admin.barang.index', compact('barangs', 'summary', 'kategoris'));
    }

    /**
     * Form tambah barang baru.
     */
    public function create()
    {
        $kategoris = Barang::daftarKategori();
        $satuans   = Barang::daftarSatuan();
        $kode      = Barang::generateKode();

        return view('admin.barang.create', compact('kategoris', 'satuans', 'kode'));
    }

    /**
     * Simpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang'        => 'required|string|max:20|unique:barangs,kode_barang',
            'nama_barang'        => 'required|string|max:150',
            'kategori'           => 'required|string|max:100',
            'satuan'             => 'required|string|max:30',
            'stok_awal'          => 'required|integer|min:0',
            'stok_masuk'         => 'nullable|integer|min:0',
            'stok_keluar'        => 'nullable|integer|min:0',
            'stok_aktual'        => 'required|integer|min:0',
            'harga_beli'         => 'nullable|numeric|min:0',
            'harga_jual'         => 'nullable|numeric|min:0',
            'stok_minimum'       => 'required|integer|min:0',
            'lokasi_simpan'      => 'nullable|string|max:100',
            'tanggal_kadaluarsa' => 'nullable|date',
            'tanggal_kroscek'    => 'nullable|date',
            'petugas_kroscek'    => 'nullable|string|max:100',
            'catatan'            => 'nullable|string',
            'status'             => 'required|in:aktif,nonaktif',
        ], [
            'kode_barang.required'  => 'Kode barang wajib diisi.',
            'kode_barang.unique'    => 'Kode barang sudah digunakan.',
            'nama_barang.required'  => 'Nama barang wajib diisi.',
            'stok_awal.required'    => 'Stok awal wajib diisi.',
            'stok_aktual.required'  => 'Stok aktual wajib diisi.',
        ]);

        $validated['stok_masuk']  = $validated['stok_masuk'] ?? 0;
        $validated['stok_keluar'] = $validated['stok_keluar'] ?? 0;
        $validated['harga_beli']  = $validated['harga_beli'] ?? 0;
        $validated['harga_jual']  = $validated['harga_jual'] ?? 0;

        Barang::create($validated);

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Detail barang.
     */
    public function show(Barang $barang)
    {
        return view('admin.barang.show', compact('barang'));
    }

    /**
     * Form edit barang.
     */
    public function edit(Barang $barang)
    {
        $kategoris = Barang::daftarKategori();
        $satuans   = Barang::daftarSatuan();

        return view('admin.barang.edit', compact('barang', 'kategoris', 'satuans'));
    }

    /**
     * Update data barang.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'kode_barang'        => ['required', 'string', 'max:20', Rule::unique('barangs', 'kode_barang')->ignore($barang->id)],
            'nama_barang'        => 'required|string|max:150',
            'kategori'           => 'required|string|max:100',
            'satuan'             => 'required|string|max:30',
            'stok_awal'          => 'required|integer|min:0',
            'stok_masuk'         => 'nullable|integer|min:0',
            'stok_keluar'        => 'nullable|integer|min:0',
            'stok_aktual'        => 'required|integer|min:0',
            'harga_beli'         => 'nullable|numeric|min:0',
            'harga_jual'         => 'nullable|numeric|min:0',
            'stok_minimum'       => 'required|integer|min:0',
            'lokasi_simpan'      => 'nullable|string|max:100',
            'tanggal_kadaluarsa' => 'nullable|date',
            'tanggal_kroscek'    => 'nullable|date',
            'petugas_kroscek'    => 'nullable|string|max:100',
            'catatan'            => 'nullable|string',
            'status'             => 'required|in:aktif,nonaktif',
        ]);

        $validated['stok_masuk']  = $validated['stok_masuk'] ?? 0;
        $validated['stok_keluar'] = $validated['stok_keluar'] ?? 0;

        $barang->update($validated);

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Hapus barang (soft delete).
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('admin.barang.index')
                         ->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Update kroscek stok saja (AJAX-ready / form kecil).
     */
    public function kroscek(Request $request, Barang $barang)
    {
        $request->validate([
            'stok_aktual'     => 'required|integer|min:0',
            'petugas_kroscek' => 'required|string|max:100',
            'catatan'         => 'nullable|string',
        ]);

        $barang->update([
            'stok_aktual'     => $request->stok_aktual,
            'tanggal_kroscek' => now()->toDateString(),
            'petugas_kroscek' => $request->petugas_kroscek,
            'catatan'         => $request->catatan,
        ]);

        return redirect()->route('admin.barang.index')
                         ->with('success', "Kroscek stok {$barang->nama_barang} berhasil disimpan.");
    }
}
