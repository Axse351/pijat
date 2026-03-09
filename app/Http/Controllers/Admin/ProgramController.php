<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::latest()->paginate(12);
        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_program'    => 'required|string|max:255',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'discount_type'   => 'required|in:percent,nominal',
            'discount_value'  => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'is_active'       => 'boolean',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('programs', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function show(Program $program)
    {
        return redirect()->route('admin.programs.edit', $program);
    }

    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'nama_program'    => 'required|string|max:255',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'discount_type'   => 'required|in:percent,nominal',
            'discount_value'  => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'is_active'       => 'boolean',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($program->image) {
                Storage::disk('public')->delete($program->image);
            }
            $validated['image'] = $request->file('image')->store('programs', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        if ($program->image) {
            Storage::disk('public')->delete($program->image);
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }

    public function toggleActive(Program $program)
    {
        $program->update(['is_active' => !$program->is_active]);

        return back()->with('success', 'Status program diperbarui.');
    }
}
