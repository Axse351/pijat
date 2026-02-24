<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::latest()->get();
        return view('admin.memberships.index', compact('memberships'));
    }

    public function create()
    {
        return view('admin.memberships.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'duration_days' => 'required|integer|min:1'
        ]);

        Membership::create($request->all());

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership berhasil dibuat');
    }

    public function edit(Membership $membership)
    {
        return view('admin.memberships.edit', compact('membership'));
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'name' => 'required',
            'duration_days' => 'required|integer|min:1'
        ]);

        $membership->update($request->all());

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership berhasil diupdate');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();

        return back()->with('success', 'Membership berhasil dihapus');
    }
}
