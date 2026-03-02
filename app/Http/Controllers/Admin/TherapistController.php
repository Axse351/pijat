<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TherapistController extends Controller
{
    public function index()
    {
        $therapists = Therapist::all();
        return view('admin.therapists.index', compact('therapists'));
    }

    public function create()
    {
        return view('admin.therapists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'specialty'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'commission_percent'   => 'required|numeric|min:0|max:100',
            'is_active'            => 'required|boolean',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('therapists', 'public');
        }

        Therapist::create([
            'name'                => $validated['name'],
            'specialty'           => $validated['specialty'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'commission_percent'  => $validated['commission_percent'],
            'is_active'           => $validated['is_active'],
            'photo'               => $photoPath,
        ]);

        return redirect()->route('admin.therapists.index')
            ->with('success', 'Terapis berhasil ditambahkan.');
    }

    public function edit(Therapist $therapist)
    {
        return view('admin.therapists.edit', compact('therapist'));
    }

    public function update(Request $request, Therapist $therapist)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'specialty'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'commission_percent'   => 'required|numeric|min:0|max:100',
            'is_active'            => 'required|boolean',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $therapist->photo;

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($therapist->photo && Storage::disk('public')->exists($therapist->photo)) {
                Storage::disk('public')->delete($therapist->photo);
            }
            // Upload foto baru
            $photoPath = $request->file('photo')->store('therapists', 'public');
        }

        $therapist->update([
            'name'                => $validated['name'],
            'specialty'           => $validated['specialty'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'commission_percent'  => $validated['commission_percent'],
            'is_active'           => $validated['is_active'],
            'photo'               => $photoPath,
        ]);

        return redirect()->route('admin.therapists.index')
            ->with('success', 'Terapis berhasil diperbarui.');
    }

    public function destroy(Therapist $therapist)
    {
        // Hapus foto jika ada
        if ($therapist->photo && Storage::disk('public')->exists($therapist->photo)) {
            Storage::disk('public')->delete($therapist->photo);
        }

        $therapist->delete();
        return back()->with('success', 'Terapis berhasil dihapus.');
    }
}
