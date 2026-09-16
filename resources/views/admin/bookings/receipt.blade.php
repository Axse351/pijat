<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $booking->id }} — {{ $booking->customer->name }}</title>
    <style>
        /* ── Reset ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ── Screen preview ── */
        body {
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 24px 16px 96px;
            font-family: 'Courier New', Courier, monospace;
        }

        .screen-toolbar {
            width: 100%;
            max-width: 260px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        .btn-print,
        .btn-bt {
            padding: 10px;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: system-ui, sans-serif;
            width: 100%;
        }

        .btn-print {
            background: #4f46e5;
        }

        .btn-print:hover {
            background: #4338ca;
        }

        .btn-bt {
            background: #0891b2;
        }

        .btn-bt:hover {
            background: #0e7490;
        }

        .btn-bt:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .btn-close {
            padding: 10px 16px;
            background: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: system-ui, sans-serif;
            width: 100%;
        }

        .btn-close:hover {
            background: #d1d5db;
        }

        .bt-status {
            font-size: 11px;
            text-align: center;
            color: #64748b;
            font-family: system-ui, sans-serif;
            min-height: 16px;
        }

        /* ── Receipt paper — dibuat lebar 58mm (≈ 219px @ 96dpi) tapi
           di-scale up dikit di layar biar kebaca, aslinya tetap 58mm ── */
        .receipt {
            width: 100%;
            max-width: 260px;
            background: #fff;
            padding: 10px 8px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            border-radius: 4px;
            font-size: 10px;
            line-height: 1.45;
            color: #111;
        }

        /* ── Common elements ── */
        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .sm {
            font-size: 9px;
        }

        .m {
            font-size: 12px;
        }

        .lg {
            font-size: 11px;
        }

        .xl {
            font-size: 18px;
        }

        .separator-solid {
            border: none;
            border-top: 1px solid #111;
            margin: 5px 0;
        }

        .separator-dashed {
            border: none;
            border-top: 1px dashed #aaa;
            margin: 5px 0;
        }

        /* ── Row layout ── */
        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 4px;
        }

        .row .label {
            flex: 1;
            color: #555;
        }

        .row .val {
            text-align: right;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* ── Total box ── */
        .total-box {
            border: 1px solid #111;
            padding: 5px 6px;
            margin: 6px 0;
        }

        .total-box .total-label {
            font-size: 9px;
            color: #555;
        }

        .total-box .total-val {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        /* ── Points badge ── */
        .points-box {
            border: 1px dashed #333;
            padding: 4px 6px;
            text-align: center;
            margin: 5px 0;
        }

        /* ── Commission ── */
        .commission-section {
            margin-top: 4px;
        }

        /* ── Barcode-like ID ── */
        .booking-id {
            font-size: 15px;
            letter-spacing: 2px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* ────────────────────────────────────────
           @media print — Thermal paper 58mm
           (dipakai kalau fallback ke window.print(),
           bukan jalur utama Bluetooth ESC/POS)
        ──────────────────────────────────────── */
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }

            body {
                background: none;
                padding: 0;
                display: block;
            }

            .screen-toolbar,
            .bt-status {
                display: none !important;
            }

            .receipt {
                width: 58mm;
                max-width: 58mm;
                box-shadow: none;
                border-radius: 0;
                padding: 4px 4px;
                font-size: 9px;
            }

            .total-box .total-val {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    {{-- Toolbar --}}
    <div class="screen-toolbar">
        <button class="btn-bt" id="btnBluetoothPrint">🔵 Cetak via Bluetooth (58mm)</button>
        <div class="bt-status" id="btStatus"></div>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Biasa (fallback)</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    {{-- ═══════════ STRUK ═══════════ --}}
    <div class="receipt">

        {{-- Header --}}
        <div class="center">
            <div class="xl bold">KOICHI</div>
            <div class="m bold">Reflexology & Massage</div>
            <div class="sm">Jl. Sisingamangaraja No.16, Panjunan, Kec. Lemahwungkuk, Kota Cirebon, Jawa Barat 45112</div>
            <div class="sm">WA: 081807081000</div>
        </div>

        <hr class="separator-solid">

        {{-- Nomor & Tanggal --}}
        <div class="center">
            <div class="booking-id">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="sm">{{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d F Y, H:i') }}
            </div>
        </div>

        <hr class="separator-dashed">

        {{-- Info Booking --}}
        <div class="row">
            <span class="label">Pelanggan</span>
            <span class="val">{{ $booking->customer->name }}</span>
        </div>
        <div class="row">
            <span class="label">Terapis</span>
            <span class="val">{{ $booking->therapist->name }}</span>
        </div>
        <div class="row">
            <span class="label">Sumber</span>
            <span class="val">{{ strtoupper($booking->order_source ?? 'walkin') }}</span>
        </div>

        <hr class="separator-dashed">

        {{-- Layanan --}}
        <div class="bold sm" style="margin-bottom:4px;">LAYANAN</div>

        <div class="row">
            <span class="label" style="font-weight:600;">{{ $booking->service->name }}</span>
        </div>
        @if ($booking->service->duration)
            <div class="sm" style="color:#666;">Durasi: {{ $booking->service->duration }} menit</div>
        @endif
        @if ($booking->service->is_home_service)
            <div class="sm" style="color:#666;">Tipe: 🏠 Home Service</div>
        @endif

        <hr class="separator-dashed">

        {{-- Harga --}}
        <div class="row">
            <span class="label">Harga</span>
            <span class="val">Rp {{ number_format($booking->price, 0, ',', '.') }}</span>
        </div>

        @if ($booking->discount > 0)
            <div class="row">
                <span class="label">Diskon
                    @if ($booking->promo)
                        <span class="sm">({{ $booking->promo->name }})</span>
                    @elseif($booking->program)
                        <span class="sm">({{ $booking->program->name }})</span>
                    @endif
                </span>
                <span class="val" style="color:#c00;">- Rp
                    {{ number_format($booking->discount, 0, ',', '.') }}</span>
            </div>
        @endif

        {{-- Total --}}
        <div class="total-box">
            <div class="total-label">TOTAL BAYAR</div>
            <div class="total-val">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</div>
        </div>

        {{-- Status --}}
        <div class="row">
            <span class="label">Status</span>
            <span class="val">
                @switch($booking->status)
                    @case('completed')
                        ✅ SELESAI
                    @break

                    @case('scheduled')
                        📅 TERJADWAL
                    @break

                    @case('ongoing')
                        🔄 BERLANGSUNG
                    @break

                    @case('cancelled')
                        ❌ BATAL
                    @break

                    @default
                        {{ strtoupper($booking->status) }}
                @endswitch
            </span>
        </div>

        {{-- Poin reward --}}
        @if (($booking->service->reward_points ?? 0) > 0 && $booking->status === 'completed')
            <hr class="separator-dashed">
            <div class="points-box">
                <div class="bold">⭐ +{{ $booking->service->reward_points }} POIN DIPEROLEH</div>
                <div class="sm">Kumpulkan 10 poin → gratis 1 jam!</div>
            </div>
        @endif

        {{-- Komisi --}}
        @if ($booking->commission && $booking->status === 'completed')
            <hr class="separator-dashed">
            <div class="commission-section sm" style="color:#777;">
                <div class="row">
                    <span class="label">Komisi Terapis</span>
                    <span class="val">{{ $booking->commission->commission_percent }}%
                        = Rp {{ number_format($booking->commission->commission_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif

        <hr class="separator-solid">

        {{-- Footer --}}
        <div class="center sm">
            <div>Terima kasih telah berkunjung!</div>
            <div style="margin-top:2px;">Jika ada saran, keluhan dan kritik dapat menghubungi <br> <b style="font-size: 180%">081806031000</b> 🌸</div>
            <div style="margin-top:4px;color:#aaa;">
                Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}
            </div>
        </div>

        {{-- Cutting guide --}}
        <div style="margin-top:12px; text-align:center; color:#ccc;" class="sm">
            - - - - - - - - - - - - - -
        </div>

    </div>

    <script>
        // ═════════════════════════════════════════════════════════
        // Auto print (fallback window.print) jika ada ?autoprint=1
        // ═════════════════════════════════════════════════════════
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 400);
            });
        }

        // ═════════════════════════════════════════════════════════
        // Data booking untuk cetak ESC/POS via Bluetooth (58mm)
        // ═════════════════════════════════════════════════════════
        const strukData = {
            id: "{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}",
            tanggal: "{{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d F Y, H:i') }}",
            pelanggan: @json($booking->customer->name),
            terapis: @json($booking->therapist->name),
            sumber: "{{ strtoupper($booking->order_source ?? 'walkin') }}",
            layanan: @json($booking->service->name),
            durasi: "{{ $booking->service->duration }}",
            homeService: {{ $booking->service->is_home_service ? 'true' : 'false' }},
            harga: {{ $booking->price }},
            diskon: {{ $booking->discount }},
            diskonLabel: @json($booking->promo->name ?? $booking->program->name ?? null),
            total: {{ $booking->final_price }},
            status: "{{ strtoupper($booking->status) }}",
            poin: {{ $booking->service->reward_points ?? 0 }},
            statusCompleted: {{ $booking->status === 'completed' ? 'true' : 'false' }},
            komisiPercent: {{ $booking->commission->commission_percent ?? 0 }},
            komisiAmount: {{ $booking->commission->commission_amount ?? 0 }},
            dicetakPada: "{{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}",
        };

        // Lebar 58mm ≈ 32 karakter/baris pakai font default printer thermal.
        // Kalau hasil cetak masih ke-wrap, turunkan ke 30 atau 28.
        const LEBAR = 32;

        function rp(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        function rataKanan(kiri, kanan) {
            const spasi = LEBAR - kiri.length - kanan.length;
            return kiri + ' '.repeat(Math.max(1, spasi)) + kanan;
        }

        function tengah(teks) {
            if (teks.length >= LEBAR) return teks.slice(0, LEBAR);
            const pad = Math.max(0, Math.floor((LEBAR - teks.length) / 2));
            return ' '.repeat(pad) + teks;
        }

        function garis(char = '-') {
            return char.repeat(LEBAR);
        }

        // Pecah teks panjang jadi beberapa baris sesuai lebar kertas
        function wrapTeks(teks) {
            const kata = teks.split(' ');
            let baris = [];
            let current = '';
            kata.forEach(k => {
                if ((current + ' ' + k).trim().length > LEBAR) {
                    baris.push(current.trim());
                    current = k;
                } else {
                    current = (current + ' ' + k).trim();
                }
            });
            if (current) baris.push(current);
            return baris;
        }

        function buatTeksStruk(d) {
            let baris = [];

            baris.push(tengah('KOICHI'));
            baris.push(tengah('Reflexology & Massage'));
            baris.push(...wrapTeks('Jl. Sisingamangaraja No.16, Panjunan, Kec. Lemahwungkuk, Kota Cirebon, Jawa Barat 45112').map(tengah));
            baris.push(tengah('WA: 081807081000'));
            baris.push('');
            baris.push(tengah('#' + d.id));
            baris.push(tengah(d.tanggal));
            baris.push(garis());

            baris.push(rataKanan('Pelanggan', d.pelanggan.slice(0, LEBAR - 12)));
            baris.push(rataKanan('Terapis', d.terapis.slice(0, LEBAR - 10)));
            baris.push(rataKanan('Sumber', d.sumber));
            baris.push(garis('.'));

            baris.push('LAYANAN');
            baris.push(...wrapTeks(d.layanan));
            if (d.durasi) baris.push('Durasi: ' + d.durasi + ' menit');
            if (d.homeService) baris.push('Tipe: Home Service');
            baris.push(garis('.'));

            baris.push(rataKanan('Harga', rp(d.harga)));
            if (d.diskon > 0) {
                const label = d.diskonLabel ? ` (${d.diskonLabel})` : '';
                baris.push(...wrapTeks(rataKanan('Diskon' + label, '-' + rp(d.diskon))));
            }
            baris.push(garis());
            baris.push(rataKanan('TOTAL', rp(d.total)));
            baris.push(garis());

            baris.push(rataKanan('Status', d.status));

            if (d.poin > 0 && d.statusCompleted) {
                baris.push(garis('.'));
                baris.push(tengah('+' + d.poin + ' POIN DIPEROLEH'));
            }

            if (d.statusCompleted && d.komisiAmount > 0) {
                baris.push(garis('.'));
                baris.push(rataKanan('Komisi ' + d.komisiPercent + '%', rp(d.komisiAmount)));
            }

            baris.push(garis());
            baris.push(tengah('Terima kasih telah'));
            baris.push(tengah('berkunjung!'));
            baris.push(...wrapTeks('Jika ada saran, keluhan dan kritik dapat menghubungi').map(tengah));
            baris.push(tengah('081806031000'));
            baris.push('');
            baris.push(tengah('Dicetak: ' + d.dicetakPada));
            baris.push('');
            baris.push('');
            baris.push('');

            return baris.join('\n');
        }

        // ═════════════════════════════════════════════════════════
        // Kirim ke printer via Web Serial (Bluetooth SPP / COM port)
        // ═════════════════════════════════════════════════════════
        let savedPort = null;
        const btBtn = document.getElementById('btnBluetoothPrint');
        const btStatus = document.getElementById('btStatus');

        function setStatus(teks) {
            btStatus.textContent = teks;
        }

        async function cetakBluetoothESCPOS() {
            if (!('serial' in navigator)) {
                alert('Browser ini tidak mendukung Web Serial API. Gunakan Chrome atau Edge terbaru (bukan Firefox/Safari).');
                return;
            }

            btBtn.disabled = true;
            setStatus('Menghubungkan ke printer...');

            try {
                const port = savedPort || await navigator.serial.requestPort();
                savedPort = port;

                if (!port.readable && !port.writable) {
                    await port.open({ baudRate: 9600 });
                } else if (!port.writable) {
                    // port sempat kebuka tapi writable belum siap
                    await port.open({ baudRate: 9600 });
                }

                const teks = buatTeksStruk(strukData);
                const encoder = new TextEncoder();

                const escInit = new Uint8Array([0x1B, 0x40]); // ESC @ = initialize
                const escCut  = new Uint8Array([0x0A, 0x0A, 0x0A, 0x1D, 0x56, 0x00]); // feed + cut

                const payload = new Uint8Array([
                    ...escInit,
                    ...encoder.encode(teks),
                    ...escCut
                ]);

                const writer = port.writable.getWriter();
                await writer.write(payload);
                writer.releaseLock();

                setStatus('✅ Struk terkirim ke printer.');
            } catch (err) {
                console.error(err);
                setStatus('❌ Gagal: ' + err.message);
                alert('Gagal print: ' + err.message);
                savedPort = null;
            } finally {
                btBtn.disabled = false;
            }
        }

        btBtn.addEventListener('click', cetakBluetoothESCPOS);
    </script>

</body>

</html>
