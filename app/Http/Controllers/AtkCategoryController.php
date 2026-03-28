<?php

namespace App\Http\Controllers;

use App\Models\AtkCategory;
use Illuminate\Http\Request;

class AtkCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AtkCategory::withCount('atks')->orderBy('code');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(20);

        return view('admin.atk.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.atk.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:atk_categories,name',
            'code' => 'required|string|unique:atk_categories,code',
            'description' => 'nullable|string',
        ]);

        AtkCategory::create($validated);

        return redirect()->route('admin.atk-categories.index')
            ->with('success', 'Kategori ATK berhasil ditambahkan!');
    }

    public function show(AtkCategory $atkCategory)
    {
        $atkCategory->load('atks');

        return view('admin.atk.categories.show', compact('atkCategory'));
    }

    public function edit(AtkCategory $atkCategory)
    {
        return view('admin.atk.categories.edit', compact('atkCategory'));
    }

    public function update(Request $request, AtkCategory $atkCategory)
    {
        $validated = $request->validate([
            'name' => "required|string|unique:atk_categories,name,{$atkCategory->id}",
            'code' => "required|string|unique:atk_categories,code,{$atkCategory->id}",
            'description' => 'nullable|string',
        ]);

        $atkCategory->update($validated);

        return redirect()->route('admin.atk-categories.index')
            ->with('success', 'Kategori ATK berhasil diperbarui!');
    }

    public function destroy(AtkCategory $atkCategory)
    {
        if ($atkCategory->atks()->exists()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus kategori yang masih memiliki item ATK.']);
        }

        $atkCategory->delete();

        return redirect()->route('admin.atk-categories.index')
            ->with('success', 'Kategori ATK berhasil dihapus!');
    }
}
