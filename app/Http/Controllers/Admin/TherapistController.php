<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Therapist;
use Illuminate\Http\Request;

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
        Therapist::create($request->all());

        return redirect()->route('admin.therapists.index');
    }

    public function edit(Therapist $therapist)
    {
        return view('admin.therapists.edit', compact('therapist'));
    }

    public function update(Request $request, Therapist $therapist)
    {
        $therapist->update($request->all());
        return redirect()->route('admin.therapists.index');
    }

    public function destroy(Therapist $therapist)
    {
        $therapist->delete();
        return back();
    }
}
