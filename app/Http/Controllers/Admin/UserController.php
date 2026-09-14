<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (admin & kasir).
     */
    public function index(): View
    {
        $users = User::whereIn('role', ['admin', 'kasir'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tampilkan form tambah user baru.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru (admin atau kasir).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:admin,kasir'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Hapus user (opsional, jaga-jaga jangan hapus diri sendiri).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
