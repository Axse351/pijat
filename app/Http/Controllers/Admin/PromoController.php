<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::latest()->get();
        return view('admin.promos.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:promos,code',
            'nama_promo' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Promo::create([
            'code' => $request->code,
            'nama_promo' => $request->nama_promo,
            'discount' => $request->discount,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promos.show', compact('promo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promos.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $promo = Promo::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:promos,code,' . $promo->id,
            'nama_promo' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $promo->update([
            'code' => $request->code,
            'nama_promo' => $request->nama_promo,
            'discount' => $request->discount,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}
