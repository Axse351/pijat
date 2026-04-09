<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    ✏️ Pengaturan Konten Website
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola semua teks yang tampil di halaman utama (landing page)
                </p>
            </div>
            <a href="{{ route('welcome') }}" target="_blank"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Preview Website
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2"><span>⚠</span> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                {{-- ── TAB NAVIGATION ── --}}
                <div class="flex overflow-x-auto border-b border-gray-100 dark:border-gray-700 px-1" id="contentTabs">
                    @php
                        $tabs = [
                            ['id' => 'hero', 'label' => 'Hero', 'icon' => '🏠'],
                            ['id' => 'navbar', 'label' => 'Navbar', 'icon' => '🔗'],
                            ['id' => 'layanan', 'label' => 'Layanan', 'icon' => '💆'],
                            ['id' => 'terapis', 'label' => 'Terapis', 'icon' => '👥'],
                            ['id' => 'tentang', 'label' => 'Tentang', 'icon' => '🏅'],
                            ['id' => 'booking', 'label' => 'Booking', 'icon' => '📅'],
                            ['id' => 'footer', 'label' => 'Footer', 'icon' => '📌'],
                        ];
                    @endphp
                    @foreach ($tabs as $tab)
                        <button type="button" onclick="switchTab('{{ $tab['id'] }}')" id="tab-{{ $tab['id'] }}"
                            class="tab-btn flex-shrink-0 flex items-center gap-1.5 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                                {{ $loop->first
                                    ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            <span>{{ $tab['icon'] }}</span>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.content.update') }}" id="contentForm">
                    @csrf
                    @method('PUT')

                    {{-- ════════════════════ HERO ════════════════════ --}}
                    <div id="panel-hero" class="tab-panel p-6 space-y-5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">🏠</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Section Hero</h3>
                            <span class="ml-auto text-xs text-gray-400">Bagian paling atas halaman</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Badge
                                    Atas</label>
                                <input type="text" name="hero_badge"
                                    value="{{ old('hero_badge', $content['hero_badge'] ?? 'Buka Setiap Hari · 09.00 – 20.00') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <p class="text-xs text-gray-400 mt-1">Teks kecil di atas judul utama</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    — Bagian Biasa</label>
                                <input type="text" name="hero_title_plain"
                                    value="{{ old('hero_title_plain', $content['hero_title_plain'] ?? 'Temukan') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <p class="text-xs text-gray-400 mt-1">Teks sebelum kata miring</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    — Kata Miring (warna aksen)</label>
                                <input type="text" name="hero_title_italic"
                                    value="{{ old('hero_title_italic', $content['hero_title_italic'] ?? 'Kedamaian') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <p class="text-xs text-gray-400 mt-1">Kata yang dicetak miring berwarna terracotta</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    — Baris Kedua</label>
                                <input type="text" name="hero_title_line2"
                                    value="{{ old('hero_title_line2', $content['hero_title_line2'] ?? 'di Tengah Kesibukan') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Subtitle
                                / Deskripsi</label>
                            <textarea name="hero_subtitle" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm resize-none">{{ old('hero_subtitle', $content['hero_subtitle'] ?? 'Layanan spa & terapi profesional untuk memulihkan tubuh, pikiran, dan jiwa Anda. Dipercaya lebih dari 500 pelanggan setia.') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Teks
                                    Tombol Utama</label>
                                <input type="text" name="hero_btn_primary"
                                    value="{{ old('hero_btn_primary', $content['hero_btn_primary'] ?? 'Booking Sekarang') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Teks
                                    Tombol Sekunder</label>
                                <input type="text" name="hero_btn_secondary"
                                    value="{{ old('hero_btn_secondary', $content['hero_btn_secondary'] ?? 'Lihat Layanan') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
                            <p
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                📊 Statistik (3 angka di bawah tombol)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach ([['stat_1', '500+', 'Pelanggan Puas'], ['stat_2', '15+', 'Terapis Bersertifikat'], ['stat_3', '8+', 'Jenis Layanan']] as [$key, $defNum, $defLabel])
                                    <div class="space-y-2">
                                        <label class="text-xs text-gray-400">Statistik {{ $loop->iteration }}</label>
                                        <input type="text" name="{{ $key }}_num"
                                            value="{{ old($key . '_num', $content[$key . '_num'] ?? $defNum) }}"
                                            placeholder="Angka (mis: 500+)"
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                        <input type="text" name="{{ $key }}_label"
                                            value="{{ old($key . '_label', $content[$key . '_label'] ?? $defLabel) }}"
                                            placeholder="Label"
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ════════════════════ NAVBAR ════════════════════ --}}
                    <div id="panel-navbar" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">🔗</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Navbar & Teks Menu</h3>
                            <span class="ml-auto text-xs text-gray-400">Navigasi bagian atas</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ([['nav_layanan', 'Label Menu: Layanan', 'Layanan'], ['nav_terapis', 'Label Menu: Terapis', 'Terapis'], ['nav_jadwal', 'Label Menu: Jadwal', 'Jadwal'], ['nav_tentang', 'Label Menu: Tentang', 'Tentang'], ['nav_booking', 'Label Menu: Booking', 'Booking'], ['nav_cta', 'Tombol Masuk (CTA Navbar)', 'Masuk']] as [$name, $label, $default])
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                                    <input type="text" name="{{ $name }}"
                                        value="{{ old($name, $content[$name] ?? $default) }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ════════════════════ LAYANAN ════════════════════ --}}
                    <div id="panel-layanan" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">💆</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Section Layanan</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Eyebrow
                                    (teks kecil atas)</label>
                                <input type="text" name="layanan_eyebrow"
                                    value="{{ old('layanan_eyebrow', $content['layanan_eyebrow'] ?? 'Layanan Kami') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 1</label>
                                <input type="text" name="layanan_title_1"
                                    value="{{ old('layanan_title_1', $content['layanan_title_1'] ?? 'Pilihan Terapi') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 2</label>
                                <input type="text" name="layanan_title_2"
                                    value="{{ old('layanan_title_2', $content['layanan_title_2'] ?? 'Terbaik untuk Anda') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea name="layanan_sub" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('layanan_sub', $content['layanan_sub'] ?? 'Setiap layanan dirancang oleh terapis bersertifikat menggunakan teknik terbaik dan bahan alami pilihan.') }}</textarea>
                        </div>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm text-blue-700 dark:text-blue-300">
                            💡 Data layanan (nama, harga, durasi) dikelola di menu
                            <a href="{{ route('admin.services.index') }}" class="font-semibold underline">Manajemen
                                Layanan</a>.
                        </div>
                    </div>

                    {{-- ════════════════════ TERAPIS ════════════════════ --}}
                    <div id="panel-terapis" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">👥</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Section Terapis</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Eyebrow</label>
                                <input type="text" name="terapis_eyebrow"
                                    value="{{ old('terapis_eyebrow', $content['terapis_eyebrow'] ?? 'Tim Kami') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 1</label>
                                <input type="text" name="terapis_title_1"
                                    value="{{ old('terapis_title_1', $content['terapis_title_1'] ?? 'Terapis Profesional') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 2</label>
                                <input type="text" name="terapis_title_2"
                                    value="{{ old('terapis_title_2', $content['terapis_title_2'] ?? '& Bersertifikat') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea name="terapis_sub" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('terapis_sub', $content['terapis_sub'] ?? 'Setiap terapis kami telah melewati pelatihan intensif dan memiliki sertifikasi resmi.') }}</textarea>
                        </div>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm text-blue-700 dark:text-blue-300">
                            💡 Data terapis dikelola di menu
                            <a href="{{ route('admin.therapists.index') }}" class="font-semibold underline">Manajemen
                                Terapis</a>.
                        </div>
                    </div>

                    {{-- ════════════════════ TENTANG ════════════════════ --}}
                    <div id="panel-tentang" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">🏅</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Section Tentang / Kenapa Kami
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Eyebrow</label>
                                <input type="text" name="tentang_eyebrow"
                                    value="{{ old('tentang_eyebrow', $content['tentang_eyebrow'] ?? 'Kenapa Kami') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 1</label>
                                <input type="text" name="tentang_title_1"
                                    value="{{ old('tentang_title_1', $content['tentang_title_1'] ?? 'Pengalaman Spa yang') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 2</label>
                                <input type="text" name="tentang_title_2"
                                    value="{{ old('tentang_title_2', $content['tentang_title_2'] ?? 'Berbeda dari yang Lain') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea name="tentang_sub" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('tentang_sub', $content['tentang_sub'] ?? 'Kami berkomitmen memberikan pengalaman wellness terbaik dengan standar pelayanan tertinggi.') }}</textarea>
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700">
                                <p
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    6 Kartu Keunggulan</p>
                            </div>
                            @php
                                $whyCards = [
                                    [
                                        'key' => 'why_1',
                                        'icon' => '🏅',
                                        'title' => 'Terapis Bersertifikat',
                                        'text' =>
                                            'Semua terapis kami bersertifikat nasional & internasional dengan pengalaman minimal 3 tahun.',
                                    ],
                                    [
                                        'key' => 'why_2',
                                        'icon' => '🌿',
                                        'title' => 'Bahan Alami Premium',
                                        'text' =>
                                            'Kami hanya menggunakan produk organik berkualitas tinggi yang aman untuk kulit Anda.',
                                    ],
                                    [
                                        'key' => 'why_3',
                                        'icon' => '📅',
                                        'title' => 'Booking Mudah',
                                        'text' =>
                                            'Pesan layanan kapan saja, di mana saja — tanpa perlu daftar akun terlebih dahulu.',
                                    ],
                                    [
                                        'key' => 'why_4',
                                        'icon' => '💆',
                                        'title' => 'Privasi Terjaga',
                                        'text' =>
                                            'Ruangan terapi privat yang tenang dan nyaman untuk pengalaman terbaik Anda.',
                                    ],
                                    [
                                        'key' => 'why_5',
                                        'icon' => '⏰',
                                        'title' => 'Fleksibel',
                                        'text' =>
                                            'Tersedia dari pukul 09.00–20.00 setiap hari, termasuk akhir pekan dan hari libur.',
                                    ],
                                    [
                                        'key' => 'why_6',
                                        'icon' => '💎',
                                        'title' => 'Harga Transparan',
                                        'text' =>
                                            'Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga yang Anda bayar.',
                                    ],
                                ];
                            @endphp
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($whyCards as $i => $card)
                                    <div class="p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-base">{{ $card['icon'] }}</span>
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Kartu
                                                {{ $i + 1 }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Ikon (emoji)</label>
                                                <input type="text" name="{{ $card['key'] }}_icon"
                                                    value="{{ old($card['key'] . '_icon', $content[$card['key'] . '_icon'] ?? $card['icon']) }}"
                                                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Judul Kartu</label>
                                                <input type="text" name="{{ $card['key'] }}_title"
                                                    value="{{ old($card['key'] . '_title', $content[$card['key'] . '_title'] ?? $card['title']) }}"
                                                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Teks Deskripsi</label>
                                                <input type="text" name="{{ $card['key'] }}_text"
                                                    value="{{ old($card['key'] . '_text', $content[$card['key'] . '_text'] ?? $card['text']) }}"
                                                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ════════════════════ BOOKING ════════════════════ --}}
                    <div id="panel-booking" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">📅</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Section Booking</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Eyebrow</label>
                                <input type="text" name="booking_eyebrow"
                                    value="{{ old('booking_eyebrow', $content['booking_eyebrow'] ?? 'Reservasi Online') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 1</label>
                                <input type="text" name="booking_title_1"
                                    value="{{ old('booking_title_1', $content['booking_title_1'] ?? 'Booking Tanpa') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Baris 2</label>
                                <input type="text" name="booking_title_2"
                                    value="{{ old('booking_title_2', $content['booking_title_2'] ?? 'Perlu Daftar Akun') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea name="booking_sub" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('booking_sub', $content['booking_sub'] ?? 'Cukup isi formulir di samping dan tim kami akan mengkonfirmasi jadwal Anda via WhatsApp dalam 30 menit.') }}</textarea>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-4">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                3 Fitur Keunggulan Booking</p>
                            @php
                                $bookingFeatures = [
                                    [
                                        'key' => 'bf_1',
                                        'icon' => '⚡',
                                        'title' => 'Konfirmasi Cepat',
                                        'desc' => 'Tim kami menghubungi Anda dalam 30 menit setelah booking diterima.',
                                    ],
                                    [
                                        'key' => 'bf_2',
                                        'icon' => '🔒',
                                        'title' => 'Data Aman',
                                        'desc' => 'Informasi Anda hanya digunakan untuk keperluan konfirmasi booking.',
                                    ],
                                    [
                                        'key' => 'bf_3',
                                        'icon' => '🔄',
                                        'title' => 'Reschedule Gratis',
                                        'desc' => 'Ubah jadwal maksimal H-1 sebelum sesi dimulai, tanpa biaya.',
                                    ],
                                ];
                            @endphp
                            @foreach ($bookingFeatures as $i => $feat)
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Ikon
                                            {{ $i + 1 }}</label>
                                        <input type="text" name="{{ $feat['key'] }}_icon"
                                            value="{{ old($feat['key'] . '_icon', $content[$feat['key'] . '_icon'] ?? $feat['icon']) }}"
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Judul</label>
                                        <input type="text" name="{{ $feat['key'] }}_title"
                                            value="{{ old($feat['key'] . '_title', $content[$feat['key'] . '_title'] ?? $feat['title']) }}"
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Deskripsi</label>
                                        <input type="text" name="{{ $feat['key'] }}_desc"
                                            value="{{ old($feat['key'] . '_desc', $content[$feat['key'] . '_desc'] ?? $feat['desc']) }}"
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Judul
                                    Form Booking</label>
                                <input type="text" name="booking_form_title"
                                    value="{{ old('booking_form_title', $content['booking_form_title'] ?? 'Buat Reservasi') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Subtitle
                                    Form</label>
                                <input type="text" name="booking_form_sub"
                                    value="{{ old('booking_form_sub', $content['booking_form_sub'] ?? 'Isi data di bawah ini. Anda tidak perlu membuat akun.') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ════════════════════ FOOTER ════════════════════ --}}
                    <div id="panel-footer" class="tab-panel p-6 space-y-5 hidden">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">📌</span>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Footer</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Nama
                                    Brand</label>
                                <input type="text" name="footer_brand"
                                    value="{{ old('footer_brand', $content['footer_brand'] ?? 'Koichi') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Nama
                                    Brand (aksen miring)</label>
                                <input type="text" name="footer_brand_accent"
                                    value="{{ old('footer_brand_accent', $content['footer_brand_accent'] ?? 'Spa') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Tagline
                                Footer</label>
                            <textarea name="footer_tagline" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('footer_tagline', $content['footer_tagline'] ?? 'Wellness & Terapi Profesional. Hadir untuk memulihkan keseimbangan tubuh dan pikiran Anda.') }}</textarea>
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700">
                                <p
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    📞 Kontak & Info</p>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">📍 Alamat</label>
                                    <input type="text" name="footer_address"
                                        value="{{ old('footer_address', $content['footer_address'] ?? 'Jl. Melati Raya No. 47, Cirebon') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">📞 Nomor Telepon</label>
                                    <input type="text" name="footer_phone"
                                        value="{{ old('footer_phone', $content['footer_phone'] ?? '0821-5567-3894') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">✉ Email</label>
                                    <input type="text" name="footer_email"
                                        value="{{ old('footer_email', $content['footer_email'] ?? 'hello@koichispa.id') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">⏰ Jam Operasional</label>
                                    <input type="text" name="footer_hours"
                                        value="{{ old('footer_hours', $content['footer_hours'] ?? '09.00 – 20.00') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs text-gray-400 mb-1">Copyright — teks kanan
                                        bawah</label>
                                    <input type="text" name="footer_copyright"
                                        value="{{ old('footer_copyright', $content['footer_copyright'] ?? 'Dibuat dengan ❤ untuk kesehatan Anda') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── STICKY SAVE BAR ── --}}
                    <div
                        class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-6 py-4 flex items-center gap-4 z-10">
                        <p class="text-xs text-gray-400 hidden sm:block">
                            Perubahan akan langsung tampil di halaman website setelah disimpan.
                        </p>
                        <div class="flex gap-3 ml-auto">
                            <button type="button" onclick="resetForm()"
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                                Reset
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Semua
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(id) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                b.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            document.getElementById('panel-' + id).classList.remove('hidden');
            const btn = document.getElementById('tab-' + id);
            btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            btn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
        }

        function resetForm() {
            if (confirm('Reset semua perubahan yang belum disimpan?')) {
                document.getElementById('contentForm').reset();
            }
        }
    </script>
</x-app-layout>
