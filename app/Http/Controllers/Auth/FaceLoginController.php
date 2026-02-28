<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FaceLoginController extends Controller
{
    /**
     * Tampilkan halaman face login
     */
    public function showFaceLogin()
    {
        return view('auth.face-login');
    }

    /**
     * Verifikasi face dan liveness
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'face_descriptor' => 'required|array',
                'liveness_score' => 'required|numeric|min:0.7', // Minimum 70% confidence untuk liveness
                'email' => 'required|email|exists:users,email',
            ], [
                'liveness_score.min' => 'Wajah tidak terdeteksi sebagai hidup. Coba lagi dengan kedipkan mata atau gelengkan kepala.',
                'face_descriptor.required' => 'Wajah tidak terdeteksi. Posisikan wajah Anda di tengah kamera.',
            ]);

            $user = User::where('email', $request->email)->first();

            // Cek apakah user sudah meregistrasi wajah
            if (!$user->face_embedding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah belum terdaftar. Silakan registrasi wajah terlebih dahulu.',
                    'require_registration' => true
                ], 422);
            }

            // Bandingkan face descriptor
            $storedDescriptor = json_decode($user->face_embedding, true);
            $similarity = $this->calculateSimilarity($request->face_descriptor, $storedDescriptor);

            // Threshold untuk kesamaan wajah (0.6 = 60%)
            if ($similarity < 0.6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok. Silakan coba lagi.',
                    'similarity' => $similarity
                ], 401);
            }

            // Jika semua validasi berhasil
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'similarity' => $similarity,
                'redirect' => route('dashboard')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrasi wajah baru
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'face_descriptor' => 'required|array',
                'liveness_score' => 'required|numeric|min:0.7',
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Simpan face embedding
            $user->update([
                'face_embedding' => json_encode($request->face_descriptor),
                'face_registered_at' => now(),
                'face_liveness_enabled' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil terdaftar!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal registrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hitung kesamaan antara dua face descriptor menggunakan Euclidean distance
     */
    private function calculateSimilarity(array $descriptor1, array $descriptor2): float
    {
        if (count($descriptor1) !== count($descriptor2)) {
            return 0;
        }

        $sumSquaredDifference = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $difference = $descriptor1[$i] - $descriptor2[$i];
            $sumSquaredDifference += $difference * $difference;
        }

        $distance = sqrt($sumSquaredDifference);

        // Konversi distance ke similarity score (0-1)
        // Jika distance = 0 (identik), similarity = 1
        // Semakin besar distance, semakin kecil similarity
        return 1 / (1 + $distance);
    }
}
