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
            padding: 24px 16px;
            font-family: 'Courier New', Courier, monospace;
        }

        .screen-toolbar {
            width: 100%;
            max-width: 360px;
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .btn-print {
            flex: 1;
            padding: 10px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: system-ui, sans-serif;
        }

        .btn-print:hover {
            background: #4338ca;
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
        }

        .btn-close:hover {
            background: #d1d5db;
        }

        /* ── Receipt paper ── */
        .receipt {
            width: 100%;
            max-width: 300px;
            background: #fff;
            padding: 16px 14px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.5;
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
            font-size: 10px;
        }

        .lg {
            font-size: 13px;
        }

        .xl {
            font-size: 15px;
        }

        .separator-solid {
            border: none;
            border-top: 1px solid #111;
            margin: 6px 0;
        }

        .separator-dashed {
            border: none;
            border-top: 1px dashed #aaa;
            margin: 6px 0;
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
            padding: 6px 8px;
            margin: 8px 0;
        }

        .total-box .total-label {
            font-size: 10px;
            color: #555;
        }

        .total-box .total-val {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* ── Points badge ── */
        .points-box {
            border: 1px dashed #333;
            padding: 5px 8px;
            text-align: center;
            margin: 6px 0;
        }

        /* ── Commission (hidden on print unless admin mode) ── */
        .commission-section {
            margin-top: 4px;
        }

        /* ── Barcode-like ID ── */
        .booking-id {
            font-size: 18px;
            letter-spacing: 3px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* ────────────────────────────────────────
           @media print — Thermal paper 80mm
        ──────────────────────────────────────── */
        @media print {
            @page {
                size: 80mm auto;
                /* lebar 80mm, tinggi auto */
                margin: 0;
            }

            body {
                background: none;
                padding: 0;
                display: block;
            }

            .screen-toolbar {
                display: none !important;
            }

            .receipt {
                width: 80mm;
                max-width: 80mm;
                box-shadow: none;
                border-radius: 0;
                padding: 8px 10px;
                font-size: 11px;
            }

            .total-box .total-val {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    {{-- Toolbar (hanya muncul di layar, bukan di print) --}}
    <div class="screen-toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    {{-- ═══════════ STRUK ═══════════ --}}
    <div class="receipt">

        {{-- Header --}}
        <div class="center">
            <div class="xl bold">KOICHI</div>
            <div class="sm">Refleksi & Perawatan</div>
            <div class="sm">Jl. Contoh No. 1, Kota Anda</div>
            <div class="sm">WA: 08123456789</div>
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

        {{-- Komisi (hanya info internal, bisa dihapus jika tidak mau tampil di struk) --}}
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
            <div style="margin-top:2px;">Semoga merasa lebih segar 🌸</div>
            <div style="margin-top:4px;color:#aaa;">
                Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}
            </div>
        </div>

        {{-- Cutting guide (hanya tampil saat print) --}}
        <div style="margin-top:12px; text-align:center; color:#ccc;" class="sm">
            - - - - - - - - - - - - - - - -
        </div>

    </div>

    <script>
        // Auto print jika ada query ?autoprint=1
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 400);
            });
        }
    </script>

</body>

</html>
