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
    /*
    |--------------------------------------------------------------------------
    | CREATE - SHOW REGISTER FACE PAGE
    |--------------------------------------------------------------------------
    */

    public function create(Therapist $therapist)
    {
        // Ambil existing face data jika ada
        $faceData = $therapist->faceData;

        return view('admin.attendances.register-face', compact('therapist', 'faceData'));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE - SAVE FACE DATA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, Therapist $therapist)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
            'embeddings' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            // Hapus image lama jika ada
            if ($therapist->faceData && $therapist->faceData->reference_image) {
                Storage::disk('public')->delete($therapist->faceData->reference_image);
            }

            // Simpan image baru
            $path = $request->file('image')->store('faces/reference', 'public');

            // Update atau create face data
            $therapist->faceData()->updateOrCreate(
                ['therapist_id' => $therapist->id],
                [
                    'face_embeddings' => $request->embeddings,
                    'reference_image' => $path,
                    'samples_count' => count($request->embeddings),
                    'status' => 'pending' // Perlu verifikasi admin
                ]
            );

            DB::commit();

            return back()->with('success', 'Data wajah berhasil disimpan. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->with('error', 'Gagal menyimpan data wajah: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY - VERIFY FACE DATA (ADMIN ONLY)
    |--------------------------------------------------------------------------
    */

    public function verify(Therapist $therapist)
    {
        try {
            $faceData = $therapist->faceData;

            if (!$faceData) {
                return back()->with('error', 'Data wajah tidak ditemukan.');
            }

            // Update status menjadi verified
            $faceData->update([
                'status' => 'verified'
            ]);

            return back()->with('success', 'Wajah ' . $therapist->name . ' berhasil diverifikasi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memverifikasi wajah: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT - REJECT FACE DATA (ADMIN)
    |--------------------------------------------------------------------------
    */

    public function reject(Therapist $therapist, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            $faceData = $therapist->faceData;

            if (!$faceData) {
                return back()->with('error', 'Data wajah tidak ditemukan.');
            }

            // Update status menjadi rejected dan simpan alasan
            $faceData->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);

            return back()->with('success', 'Wajah ditolak. Therapist harus mendaftar ulang.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak wajah: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY - DELETE FACE DATA
    |--------------------------------------------------------------------------
    */

    public function destroy(Therapist $therapist)
    {
        try {
            $faceData = $therapist->faceData;

            if (!$faceData) {
                return back()->with('error', 'Data wajah tidak ditemukan.');
            }

            // Hapus file image dari storage
            if ($faceData->reference_image) {
                Storage::disk('public')->delete($faceData->reference_image);
            }

            // Hapus record dari database
            $faceData->delete();

            return back()->with('success', 'Data wajah berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data wajah: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW - SHOW FACE DATA DETAIL (Optional)
    |--------------------------------------------------------------------------
    */

    public function show(Therapist $therapist)
    {
        $faceData = $therapist->faceData;

        if (!$faceData) {
            return back()->with('error', 'Data wajah tidak ditemukan.');
        }

        return view('admin.attendances.face-detail', compact('therapist', 'faceData'));
    }
}
