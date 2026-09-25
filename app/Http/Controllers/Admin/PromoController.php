<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::latest()->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => 'required|unique:promos,code',
            'nama_promo'    => 'required|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount'      => 'required|numeric|min:0',
            'max_discount'  => 'nullable|numeric|min:0',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        Promo::create([
            'code'          => $validated['code'],
            'nama_promo'    => $validated['nama_promo'],
            'discount_type' => $validated['discount_type'],
            'discount'      => $validated['discount'],
            'max_discount'  => $validated['discount_type'] === 'percent' ? ($validated['max_discount'] ?? null) : null,
            'status'        => $validated['status'],
        ]);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promos.show', compact('promo'));
    }

    public function edit(string $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, string $id)
    {
        $promo = Promo::findOrFail($id);

        $validated = $request->validate([
            'code'          => 'required|unique:promos,code,' . $promo->id,
            'nama_promo'    => 'required|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount'      => 'required|numeric|min:0',
            'max_discount'  => 'nullable|numeric|min:0',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $promo->update([
            'code'          => $validated['code'],
            'nama_promo'    => $validated['nama_promo'],
            'discount_type' => $validated['discount_type'],
            'discount'      => $validated['discount'],
            'max_discount'  => $validated['discount_type'] === 'percent' ? ($validated['max_discount'] ?? null) : null,
            'status'        => $validated['status'],
        ]);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}
