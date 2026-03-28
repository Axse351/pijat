<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Pelanggan</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $customer->user->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                    ← Kembali
                </a>
                <a href="{{ route('admin.customers.membership.create', $customer) }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    + Assign Membership
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Card membership aktif --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Membership Aktif</p>

                @if ($activeMembership)
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $activeMembership->membership->name }}
                            </span>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $activeMembership->start_date->format('d M Y') }}
                                –
                                {{ $activeMembership->end_date->format('d M Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                            Aktif
                        </span>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Tidak ada membership aktif saat ini.</p>
                @endif
            </div>

            {{-- Riwayat --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Membership</h3>
                </div>

                @if ($histories->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Mulai</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Berakhir</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($histories as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $item->membership->name }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $item->start_date->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $item->end_date->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.customers.membership.edit', [$customer, $item]) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.membership.destroy', [$customer, $item]) }}"
                                                    onsubmit="return confirm('Hapus membership ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400 text-sm">
                        Belum ada riwayat membership.
                    </div>
                @endif
            </div>

        </div>
    </div>
    {{-- ── WELCOME MEMBERSHIP MODAL ──────────────────────────────────────── --}}
    @if (session('welcome_membership'))
        @php $wm = session('welcome_membership'); @endphp

        <div id="welcomeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">

            <div
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden
                animate-[fadeInUp_0.4s_ease]">

                {{-- Header gradient --}}
                <div
                    class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 px-8 py-8 text-center relative">
                    {{-- Sparkle decorations --}}
                    <div class="absolute top-3 left-5 text-white/40 text-2xl select-none">✦</div>
                    <div class="absolute top-6 right-8 text-white/30 text-lg select-none">✦</div>
                    <div class="absolute bottom-4 left-10 text-white/20 text-sm select-none">✦</div>

                    <div
                        class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 ring-4 ring-white/30">
                        <span class="text-4xl">👑</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">
                        Selamat Datang, Member!
                    </h2>
                    <p class="text-white/80 text-sm mt-1">
                        {{ $wm['membership_name'] }}
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-8 py-6 text-center">
                    <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed">
                        Hei, <span
                            class="font-bold text-indigo-600 dark:text-indigo-400">{{ $wm['customer_name'] }}</span>!
                        🎉
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 leading-relaxed">
                        Kamu kini resmi menjadi anggota
                        <span
                            class="font-semibold text-gray-700 dark:text-gray-200">{{ $wm['membership_name'] }}</span>.
                        Nikmati semua keuntungan eksklusif yang telah kami siapkan untukmu.
                    </p>

                    {{-- Divider --}}
                    <div class="my-5 flex items-center gap-3">
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                        <span class="text-xs text-gray-400">Detail Membership</span>
                        <div class="flex-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                    </div>

                    {{-- Info chips --}}
                    <div class="flex justify-center gap-4 flex-wrap">
                        <div class="flex flex-col items-center bg-indigo-50 dark:bg-indigo-900/30 rounded-xl px-5 py-3">
                            <span class="text-xl mb-1">📅</span>
                            <span class="text-xs text-gray-400">Berlaku hingga</span>
                            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">
                                {{ $wm['end_date'] }}
                            </span>
                        </div>
                        <div class="flex flex-col items-center bg-purple-50 dark:bg-purple-900/30 rounded-xl px-5 py-3">
                            <span class="text-xl mb-1">💎</span>
                            <span class="text-xs text-gray-400">Status</span>
                            <span class="text-sm font-bold text-purple-600 dark:text-purple-400 mt-0.5">
                                Aktif
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-5 italic">
                        "Terima kasih telah mempercayakan perawatan Anda kepada kami. 💆‍♀️"
                    </p>
                </div>

                {{-- Footer --}}
                {{-- Footer --}}
                <div class="px-8 pb-6 flex flex-col gap-3">

                    {{-- Tombol kirim WA --}}
                    @if (!empty($wm['phone']))
                        @php
                            $phone = preg_replace('/\D/', '', $wm['phone']);
                            // Konversi 08xx → 628xx
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }

                            $pesan = urlencode(
                                "Halo {$wm['customer_name']}! 👋\n\n" .
                                    "Selamat ya, kamu kini resmi menjadi Member *{$wm['membership_name']}* di Koichi! 🎉👑\n\n" .
                                    "✨ Nikmati berbagai keuntungan eksklusif yang sudah kami siapkan untukmu.\n" .
                                    "📅 Membership kamu berlaku hingga *{$wm['end_date']}*.\n\n" .
                                    "Terima kasih telah mempercayakan perawatan kepada kami. Kami siap memanjakan kamu! 💆‍♀️\n\n" .
                                    "Salam hangat,\n_Tim Koichi_ 🌸",
                            );
                            $waUrl = "https://wa.me/{$phone}?text={$pesan}";
                        @endphp
                        <a href="{{ $waUrl }}" target="_blank"
                            class="flex items-center justify-center gap-2 w-full py-2.5
               bg-[#25D366] hover:bg-[#1ebe5d]
               text-white text-sm font-semibold rounded-xl
               transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15
                     -.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075
                     -.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059
                     -.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52
                     .149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52
                     -.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51
                     -.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372
                     -.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074
                     .149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625
                     .712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413
                     .248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.855L.057 23.882
                     a.75.75 0 00.921.921l6.086-1.458A11.945 11.945 0 0012 24c6.627 0 12-5.373
                     12-12S18.627 0 12 0zm0 21.75a9.718 9.718 0 01-4.953-1.355l-.355-.211
                     -3.674.88.896-3.595-.23-.372A9.718 9.718 0 012.25 12C2.25 6.615 6.615
                     2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z" />
                            </svg>
                            Kirim Ucapan via WhatsApp
                        </a>
                    @else
                        <p class="text-center text-xs text-gray-400 italic">
                            ⚠️ Nomor WhatsApp pelanggan belum tersimpan.
                        </p>
                    @endif

                    <button onclick="closeWelcome()"
                        class="flex-1 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600
               hover:from-indigo-600 hover:to-purple-700
               text-white text-sm font-semibold rounded-xl transition-all
               shadow-md hover:shadow-lg">
                        ✨ Oke, Terima Kasih!
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px) scale(0.96);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
        </style>

        <script>
            function closeWelcome() {
                const el = document.getElementById('welcomeModal');
                el.style.transition = 'opacity 0.25s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 260);
            }
            // Tutup jika klik backdrop
            document.getElementById('welcomeModal').addEventListener('click', function(e) {
                if (e.target === this) closeWelcome();
            });
        </script>
    @endif
</x-app-layout>
