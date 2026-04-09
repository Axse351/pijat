<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    /**
     * Tampilkan halaman pengaturan konten.
     */
    public function index()
    {
        $content = $this->loadContent();
        return view('admin.content', compact('content'));
    }

    /**
     * Simpan semua perubahan konten.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Sanitasi — hapus tag HTML berbahaya
        $data = array_map('strip_tags', $data);

        $this->saveContent($data);

        // Bersihkan cache agar landing page langsung update
        Cache::forget('site_content');

        return redirect()
            ->route('admin.content.index')
            ->with('success', 'Konten website berhasil disimpan! ✓');
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function loadContent(): array
    {
        $path = storage_path('app/content.json');

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function saveContent(array $data): void
    {
        $path = storage_path('app/content.json');
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
