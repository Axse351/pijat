<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('category')->orderBy('name')->get();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Refleksi,Minuman',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'nullable|integer|min:1',
            'sku'         => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Service::create([
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'duration'    => $request->duration ?: null,
            'sku'         => $request->sku ?: null,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Refleksi,Minuman',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'nullable|integer|min:1',
            'sku'         => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $service->update([
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'duration'    => $request->duration ?: null,
            'sku'         => $request->sku ?: null,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}
