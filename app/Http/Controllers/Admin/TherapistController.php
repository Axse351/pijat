<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TherapistController extends Controller
{
    /**
     * Display a listing of therapists.
     */
    public function index()
    {
        $therapists = Therapist::with('user')->get();
        return view('admin.therapists.index', compact('therapists'));
    }

    /**
     * Show the form for creating a new therapist.
     */
    public function create()
    {
        return view('admin.therapists.create');
    }

    /**
     * Store a newly created therapist in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'specialty'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20|unique:therapists,phone',
            'email'                => 'required|email|unique:users,email',
            'commission_percent'   => 'required|numeric|min:0|max:100',
            'is_active'            => 'required|in:0,1',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gunakan transaction untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            // 1. Buat user dengan role terapis dan password default '123456'
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'password'  => Hash::make('123456'), // Password default
                'role'      => 'terapis',
                'is_active' => (bool) $validated['is_active'],
            ]);

            // 2. Upload foto jika ada
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('therapists', 'public');
            }

            // 3. Buat terapis dengan relasi ke user
            Therapist::create([
                'name'                => $validated['name'],
                'specialty'           => $validated['specialty'] ?? null,
                'phone'               => $validated['phone'] ?? null,
                'commission_percent'  => $validated['commission_percent'],
                'is_active'           => (bool) $validated['is_active'],
                'photo'               => $photoPath,
                'user_id'             => $user->id, // Relasi ke user
            ]);

            DB::commit();

            return redirect()->route('admin.therapists.index')
                ->with('success', 'Terapis berhasil ditambahkan. User otomatis dibuat dengan email: ' . $validated['email'] . ' dan password default: 123456');
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus foto jika terjadi error
            if ($request->hasFile('photo') && isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified therapist.
     */
    public function edit(Therapist $therapist)
    {
        return view('admin.therapists.edit', compact('therapist'));
    }

    /**
     * Update the specified therapist in storage.
     */
    public function update(Request $request, Therapist $therapist)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'specialty'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20|unique:therapists,phone,' . $therapist->id,
            'email'                => 'required|email|unique:users,email,' . ($therapist->user_id ?? 'null'),
            'commission_percent'   => 'required|numeric|min:0|max:100',
            'is_active'            => 'required|in:0,1',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 1. Update user jika ada relasi
            if ($therapist->user) {
                $therapist->user->update([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'phone'     => $validated['phone'] ?? null,
                    'is_active' => (bool) $validated['is_active'],
                ]);
            } else {
                // Jika belum ada user (data lama), buat user baru
                $user = User::create([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'phone'     => $validated['phone'] ?? null,
                    'password'  => Hash::make('123456'),
                    'role'      => 'terapis',
                    'is_active' => (bool) $validated['is_active'],
                ]);
                $therapist->user_id = $user->id;
            }

            // 2. Update foto jika ada yang baru
            $photoPath = $therapist->photo;
            if ($request->hasFile('photo')) {
                // Hapus foto lama
                if ($therapist->photo && Storage::disk('public')->exists($therapist->photo)) {
                    Storage::disk('public')->delete($therapist->photo);
                }
                // Upload foto baru
                $photoPath = $request->file('photo')->store('therapists', 'public');
            }

            // 3. Update terapis
            $therapist->update([
                'name'                => $validated['name'],
                'specialty'           => $validated['specialty'] ?? null,
                'phone'               => $validated['phone'] ?? null,
                'commission_percent'  => $validated['commission_percent'],
                'is_active'           => (bool) $validated['is_active'],
                'photo'               => $photoPath,
            ]);

            DB::commit();

            return redirect()->route('admin.therapists.index')
                ->with('success', 'Terapis berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified therapist from storage.
     */
    public function destroy(Therapist $therapist)
    {
        DB::beginTransaction();

        try {
            // 1. Hapus foto
            if ($therapist->photo && Storage::disk('public')->exists($therapist->photo)) {
                Storage::disk('public')->delete($therapist->photo);
            }

            // 2. Simpan user_id sebelum delete therapist
            $userId = $therapist->user_id;

            // 3. Hapus terapis
            $therapist->delete();

            // 4. Hapus user yang terkait
            if ($userId) {
                User::find($userId)->delete();
            }

            DB::commit();

            return back()->with('success', 'Terapis dan user-nya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Reset password terapis ke default (123456)
     */
    public function resetPassword(Therapist $therapist)
    {
        try {
            if ($therapist->user) {
                $therapist->user->update([
                    'password' => Hash::make('123456'),
                ]);

                return back()->with('success', 'Password terapis berhasil direset ke 123456');
            }

            return back()->with('error', 'User tidak ditemukan untuk terapis ini.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
