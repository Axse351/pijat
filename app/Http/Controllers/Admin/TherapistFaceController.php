<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapist;
use App\Models\TherapistFaceData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TherapistFaceController extends Controller
{
    public function create(Therapist $therapist)
    {
        $faceData = $therapist->faceData;
        return view('admin.attendances.register-face', compact('therapist', 'faceData'));
    }

    public function store(Request $request, Therapist $therapist)
    {
        $request->validate([
            'image'      => 'required|image|max:5120',
            'embeddings' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Hapus image lama
            if ($therapist->faceData?->reference_image) {
                Storage::disk('public')->delete($therapist->faceData->reference_image);
            }

            $path = $request->file('image')->store('faces/reference', 'public');

            // Decode embeddings dari JSON string → array
            $embeddings = $request->embeddings;
            if (is_string($embeddings)) {
                $embeddings = json_decode($embeddings, true) ?? [];
            }
            if (!is_array($embeddings)) {
                $embeddings = [];
            }

            $therapist->faceData()->updateOrCreate(
                ['therapist_id' => $therapist->id],
                [
                    'face_embeddings' => $embeddings,  // Eloquent akan cast ke JSON otomatis
                    'reference_image' => $path,
                    'samples_count'   => count($embeddings),
                    'status'          => 'pending',
                ]
            );

            DB::commit();
            return back()->with('success', 'Data wajah berhasil disimpan. Menunggu verifikasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path)) Storage::disk('public')->delete($path);
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function verify(Therapist $therapist)
    {
        try {
            $faceData = $therapist->faceData;
            if (!$faceData) return back()->with('error', 'Data wajah tidak ditemukan.');
            $faceData->update(['status' => 'verified']);
            return back()->with('success', 'Wajah ' . $therapist->name . ' berhasil diverifikasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function reject(Therapist $therapist, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        try {
            $faceData = $therapist->faceData;
            if (!$faceData) return back()->with('error', 'Data wajah tidak ditemukan.');
            $faceData->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);
            return back()->with('success', 'Wajah ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(Therapist $therapist)
    {
        try {
            $faceData = $therapist->faceData;
            if (!$faceData) return back()->with('error', 'Data wajah tidak ditemukan.');
            if ($faceData->reference_image) Storage::disk('public')->delete($faceData->reference_image);
            $faceData->delete();
            return back()->with('success', 'Data wajah berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
