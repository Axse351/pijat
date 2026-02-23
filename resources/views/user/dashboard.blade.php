<x-app-layout>
    <x-slot name="header">
        {{-- Custom header --}}
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap');

        :root {
            --cream: #FAF7F2;
            --warm: #F2EBE0;
            --sand: #E8D9C5;
            --brown: #8B6B4A;
            --brown-dark: #5C4530;
            --gold: #C9A84C;
            --text: #2D2420;
            --text-muted: #9C8778;
            --green: #4A7C59;
            --white: #FFFFFF;
        }

        body {
            background: var(--cream);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
        }

        /* ── HERO SECTION ── */
        .user-hero {
            background: linear-gradient(135deg, var(--brown-dark) 0%, #8B5E3C 50%, var(--brown) 100%);
            padding: 36px 36px 64px;
            position: relative;
            overflow: hidden;
        }

        .user-hero::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(201,168,76,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .user-hero::after {
            content: '';
            position: absolute;
            bottom: -80px; left: 10%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(201,168,76,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .hero-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            color: rgba(255,255,255,0.7);
            letter-spacing: 1px;
        }

        .hero-brand span { color: var(--gold); }

        .hero-notif {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            position: relative;
        }

        .hero-notif-dot {
            position: absolute; top: 8px; right: 8px;
            width: 7px; height: 7px;
            background: var(--gold);
            border-radius: 50%;
        }

        .hero-greeting {
            position: relative; z-index: 1;
        }

        .hero-greeting .hi {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .hero-greeting h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 8px;
        }

        .hero-greeting p {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            max-width: 300px;
        }

        .hero-points {
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(201,168,76,0.3);
            padding: 10px 18px;
            border-radius: 30px;
        }

        .hero-points .pts {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--gold);
        }

        .hero-points .pts-label {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }

        /* ── MAIN CONTAINER ── */
        .user-container {
            max-width: 1000px;
            margin: -32px auto 0;
            padding: 0 24px 60px;
            position: relative;
            z-index: 10;
        }

        /* ── BOOKING CARD ── */
        .book-card {
            background: var(--white);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.08);
            margin-bottom: 28px;
        }

        .section-heading {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--sand);
        }

        .section-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* SERVICE CATEGORIES */
        .service-categories {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 20px;
            scrollbar-width: none;
        }

        .service-categories::-webkit-scrollbar { display: none; }

        .cat-chip {
            flex-shrink: 0;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1.5px solid var(--sand);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            font-family: 'Outfit', sans-serif;
        }

        .cat-chip.active, .cat-chip:hover {
            background: var(--brown-dark);
            border-color: var(--brown-dark);
            color: #fff;
        }

        /* SERVICE CARDS */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .service-card {
            background: var(--warm);
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            border: 1.5px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-color: var(--sand);
        }

        .service-card-img {
            height: 110px;
            background: linear-gradient(135deg, var(--sand), var(--brown) 150%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .service-card-body { padding: 14px; }

        .service-card-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .service-card-duration {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .service-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-card-price {
            font-size: 14px;
            font-weight: 600;
            color: var(--brown);
        }

        .btn-book-mini {
            background: var(--brown-dark);
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: opacity 0.2s;
        }

        .btn-book-mini:hover { opacity: 0.8; }

        /* ── TWO COLUMN ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        /* ── MY BOOKINGS ── */
        .booking-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--warm);
        }

        .booking-item:last-child { border-bottom: none; }

        .booking-date-box {
            width: 48px; height: 52px;
            background: var(--warm);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .booking-date-box .day {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--brown-dark);
            line-height: 1;
        }

        .booking-date-box .month {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
        }

        .booking-info { flex: 1; }
        .booking-info .bname { font-size: 14px; font-weight: 500; color: var(--text); }
        .booking-info .bdetail { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .booking-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .booking-badge.confirmed { background: rgba(74,124,89,0.1); color: var(--green); }
        .booking-badge.pending   { background: rgba(201,168,76,0.1); color: var(--gold); }

        /* ── PROMO CARD ── */
        .promo-card {
            background: linear-gradient(135deg, var(--brown-dark), #7A4A2A);
            border-radius: 16px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .promo-card:hover { transform: scale(1.02); }

        .promo-card::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            background: rgba(201,168,76,0.15);
            border-radius: 50%;
        }

        .promo-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--gold);
            margin-bottom: 8px;
        }

        .promo-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .promo-desc {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 16px;
        }

        .promo-code {
            background: rgba(255,255,255,0.1);
            border: 1px dashed rgba(201,168,76,0.5);
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: 2px;
        }

        /* ── REVIEW & TIPS ── */
        .tip-list { display: flex; flex-direction: column; gap: 10px; }

        .tip-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: var(--warm);
            border-radius: 10px;
        }

        .tip-icon {
            width: 36px; height: 36px;
            background: rgba(139,107,74,0.1);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .tip-text { font-size: 13px; color: var(--text-muted); line-height: 1.5; }
        .tip-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            display: none;
        }

        /* ── CTA BOOKING BUTTON ── */
        .cta-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 8px 0 32px;
        }

        .btn-book-main {
            background: linear-gradient(135deg, var(--brown-dark), #7A4A2A);
            color: #fff;
            border: none;
            padding: 16px 48px;
            border-radius: 50px;
            font-size: 16px;
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(92,69,48,0.35);
            transition: all 0.2s;
        }

        .btn-book-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(92,69,48,0.45);
        }

        /* ── TERAPIS PILIHAN ── */
        .terapis-scroll {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .terapis-scroll::-webkit-scrollbar { display: none; }

        .terapis-pick-card {
            flex-shrink: 0;
            width: 130px;
            background: var(--warm);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .terapis-pick-card:hover, .terapis-pick-card.selected {
            border-color: var(--brown);
            background: #fff;
        }

        .terapis-pick-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sand), var(--brown));
            margin: 0 auto 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
        }

        .terapis-pick-name { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
        .terapis-pick-spec { font-size: 11px; color: var(--text-muted); }

        .terapis-pick-rating {
            margin-top: 8px;
            font-size: 12px;
            color: var(--gold);
        }
    </style>

    <!-- HERO -->
    <div class="user-hero">
        <div class="hero-top">
            <span class="hero-brand">✦ Serene<span>Touch</span></span>
            <div class="hero-notif">
                <svg width="18" height="18" fill="none" stroke="rgba(255,255,255,0.7)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <div class="hero-notif-dot"></div>
            </div>
        </div>
        <div class="hero-greeting">
            <div class="hi">Halo 👋</div>
            <h1>{{ Auth::user()->name }},</h1>
            <p>Saatnya manjakan diri. Tubuh Anda layak mendapatkan istirahat terbaik.</p>
            <div class="hero-points">
                <span class="pts">240</span>
                <span class="pts-label">Poin Loyalitas</span>
                <svg width="16" height="16" fill="none" stroke="rgba(201,168,76,0.6)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="user-container">

        <!-- CTA BUTTON -->
        <div class="cta-wrap">
            <button class="btn-book-main" onclick="window.location.href='{{ route('user.booking.create') }}'">
                ✦ Pesan Sesi Sekarang
            </button>
        </div>

        <!-- LAYANAN -->
        <div class="book-card">
            <div class="section-heading">Pilih Layanan</div>
            <div class="service-categories">
                <button class="cat-chip active">Semua</button>
                <button class="cat-chip">Swedish</button>
                <button class="cat-chip">Deep Tissue</button>
                <button class="cat-chip">Refleksi</button>
                <button class="cat-chip">Aromaterapi</button>
                <button class="cat-chip">Hot Stone</button>
                <button class="cat-chip">Prenatal</button>
            </div>
            <div class="service-grid">
                <div class="service-card">
                    <div class="service-card-img">🌿</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Swedish Massage</div>
                        <div class="service-card-duration">⏱ 60 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 180.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-img">💆</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Deep Tissue</div>
                        <div class="service-card-duration">⏱ 90 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 250.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-img">🌸</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Aromaterapi</div>
                        <div class="service-card-duration">⏱ 75 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 200.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-img">🪨</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Hot Stone</div>
                        <div class="service-card-duration">⏱ 90 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 300.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-img">👣</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Refleksi Kaki</div>
                        <div class="service-card-duration">⏱ 45 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 120.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-card-img">🤱</div>
                    <div class="service-card-body">
                        <div class="service-card-name">Prenatal</div>
                        <div class="service-card-duration">⏱ 60 menit</div>
                        <div class="service-card-footer">
                            <span class="service-card-price">Rp 220.000</span>
                            <button class="btn-book-mini">Pesan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PILIH TERAPIS -->
        <div class="book-card">
            <div class="section-heading">Terapis Pilihan</div>
            <div class="terapis-scroll">
                <div class="terapis-pick-card selected">
                    <div class="terapis-pick-avatar">L</div>
                    <div class="terapis-pick-name">Lestari</div>
                    <div class="terapis-pick-spec">Swedish & Aroma</div>
                    <div class="terapis-pick-rating">★ 4.9</div>
                </div>
                <div class="terapis-pick-card">
                    <div class="terapis-pick-avatar">D</div>
                    <div class="terapis-pick-name">Dwi Astuti</div>
                    <div class="terapis-pick-spec">Deep Tissue</div>
                    <div class="terapis-pick-rating">★ 4.8</div>
                </div>
                <div class="terapis-pick-card">
                    <div class="terapis-pick-avatar">Y</div>
                    <div class="terapis-pick-name">Yuni</div>
                    <div class="terapis-pick-spec">Shiatsu & Akupresur</div>
                    <div class="terapis-pick-rating">★ 4.7</div>
                </div>
                <div class="terapis-pick-card">
                    <div class="terapis-pick-avatar">A</div>
                    <div class="terapis-pick-name">Ayu Rahayu</div>
                    <div class="terapis-pick-spec">Hot Stone</div>
                    <div class="terapis-pick-rating">★ 5.0</div>
                </div>
                <div class="terapis-pick-card">
                    <div class="terapis-pick-avatar">F</div>
                    <div class="terapis-pick-name">Fitria</div>
                    <div class="terapis-pick-spec">Prenatal</div>
                    <div class="terapis-pick-rating">★ 4.9</div>
                </div>
            </div>
        </div>

        <!-- 2 COL: MY BOOKINGS + PROMO -->
        <div class="two-col">
            <!-- My Bookings -->
            <div class="book-card">
                <div class="section-heading">Booking Saya</div>
                <div>
                    @forelse($myBookings as $booking)
                    <div class="booking-item">
                        <div class="booking-date-box">
                            <span class="day">{{ \Carbon\Carbon::parse($booking->tanggal)->format('d') }}</span>
                            <span class="month">{{ \Carbon\Carbon::parse($booking->tanggal)->format('M') }}</span>
                        </div>
                        <div class="booking-info">
                            <div class="bname">{{ $booking->layanan->nama }}</div>
                            <div class="bdetail">{{ $booking->terapis->name }} • {{ $booking->jam }}</div>
                        </div>
                        <span class="booking-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                    @empty
                    {{-- Contoh data statis --}}
                    <div class="booking-item">
                        <div class="booking-date-box">
                            <span class="day">24</span>
                            <span class="month">Jun</span>
                        </div>
                        <div class="booking-info">
                            <div class="bname">Swedish Massage 60 mnt</div>
                            <div class="bdetail">Lestari • 10:00</div>
                        </div>
                        <span class="booking-badge confirmed">Confirmed</span>
                    </div>
                    <div class="booking-item">
                        <div class="booking-date-box">
                            <span class="day">28</span>
                            <span class="month">Jun</span>
                        </div>
                        <div class="booking-info">
                            <div class="bname">Aromaterapi 75 mnt</div>
                            <div class="bdetail">Dwi Astuti • 14:30</div>
                        </div>
                        <span class="booking-badge pending">Pending</span>
                    </div>
                    @endforelse
                </div>
                <div style="margin-top:16px;">
                    <a href="{{ route('user.bookings') }}" style="font-size:13px; color:var(--brown); font-weight:500; text-decoration:none;">
                        Lihat Semua Booking →
                    </a>
                </div>
            </div>

            <!-- Promo -->
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div class="promo-card">
                    <div class="promo-label">✦ Promo Spesial</div>
                    <div class="promo-title">Hemat 25%<br>Weekend Bliss</div>
                    <div class="promo-desc">Khusus hari Sabtu & Minggu untuk semua layanan 90 menit.</div>
                    <span class="promo-code">WEEKEND25</span>
                </div>

                <!-- Tips Rileksasi -->
                <div class="book-card" style="margin-bottom:0; padding:20px 22px;">
                    <div class="section-heading" style="font-size:18px;">Tips Hari Ini</div>
                    <div class="tip-list">
                        <div class="tip-item">
                            <div class="tip-icon">💧</div>
                            <div>
                                <div class="tip-title">Hidrasi Sebelum Sesi</div>
                                <div class="tip-text">Minum 2 gelas air sebelum sesi untuk hasil optimal.</div>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon">🧘</div>
                            <div>
                                <div class="tip-title">Datang 10 Menit Lebih Awal</div>
                                <div class="tip-text">Beri waktu tubuh untuk relaksasi penuh.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Category chip toggle
        document.querySelectorAll('.cat-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.cat-chip').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        // Terapis selection
        document.querySelectorAll('.terapis-pick-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.terapis-pick-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            });
        });
    </script>
</x-app-layout>
