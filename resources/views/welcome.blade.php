<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Koichi Spa — Wellness & Terapi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --cream: #faf7f2;
            --warm: #f5ede0;
            --sand: #e8d5b7;
            --terracotta: #c4714b;
            --terra-dark: #a35a38;
            --brown: #6b4226;
            --text: #2c1f13;
            --muted: #8c7060;
            --white: #ffffff;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* ─── NAVBAR ─── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 0 40px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .3s, box-shadow .3s;
        }

        .navbar.scrolled {
            background: rgba(250, 247, 242, 0.96);
            backdrop-filter: blur(14px);
            box-shadow: 0 2px 20px rgba(107, 66, 38, .08);
        }

        .navbar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brown);
            letter-spacing: -.02em;
            text-decoration: none;
        }

        .navbar-logo span {
            color: var(--terracotta);
            font-style: italic;
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .navbar-links a {
            font-size: .875rem;
            font-weight: 500;
            color: var(--brown);
            text-decoration: none;
            opacity: .75;
            transition: opacity .2s;
        }

        .navbar-links a:hover {
            opacity: 1;
        }

        .navbar-cta {
            padding: 10px 24px;
            background: var(--terracotta);
            color: var(--white) !important;
            border-radius: 100px;
            opacity: 1 !important;
            font-weight: 600 !important;
            transition: background .2s !important;
        }

        .navbar-cta:hover {
            background: var(--terra-dark) !important;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
            background: none;
            border: none;
            outline: none;
        }

        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--brown);
            border-radius: 2px;
            transition: .3s;
        }

        .mobile-menu {
            display: none !important;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: var(--cream);
            border-top: 1px solid var(--sand);
            padding: 24px 24px 32px;
            flex-direction: column;
            gap: 0;
            z-index: 99;
            box-shadow: 0 8px 32px rgba(107, 66, 38, .12);
        }

        .mobile-menu.open {
            display: flex !important;
        }

        .mobile-menu a {
            color: var(--brown) !important;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none !important;
            padding: 12px 0;
            border-bottom: 1px solid var(--warm);
            display: block;
        }

        .mobile-menu .mobile-cta {
            margin-top: 12px;
            padding: 14px;
            text-align: center;
            background: var(--terracotta) !important;
            color: white !important;
            border-radius: 12px;
            font-weight: 600;
            border-bottom: none !important;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }

            .navbar-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }
        }

        /* ─── HERO ─── */
        #hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: var(--warm);
            padding: 100px 40px 60px;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 70% 50%, rgba(196, 113, 75, .12) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(232, 213, 183, .5) 0%, transparent 50%);
        }

        .hero-ornament {
            position: absolute;
            right: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 600px;
            height: 600px;
            border-radius: 50%;
            border: 1px solid rgba(196, 113, 75, .15);
            pointer-events: none;
        }

        .hero-ornament::before {
            content: '';
            position: absolute;
            inset: 40px;
            border-radius: 50%;
            border: 1px solid rgba(196, 113, 75, .1);
        }

        .hero-ornament::after {
            content: '';
            position: absolute;
            inset: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(196, 113, 75, .06) 0%, transparent 70%);
        }

        .hero-content {
            position: relative;
            max-width: 640px;
            animation: fadeUp .8s ease both;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(196, 113, 75, .1);
            border: 1px solid rgba(196, 113, 75, .25);
            border-radius: 100px;
            font-size: .75rem;
            font-weight: 600;
            color: var(--terracotta);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .hero-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--terracotta);
            border-radius: 50%;
            animation: blink 2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(.75);
            }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.8rem, 5vw, 4.5rem);
            line-height: 1.1;
            color: var(--brown);
            margin-bottom: 24px;
        }

        .hero-title em {
            font-style: italic;
            color: var(--terracotta);
        }

        .hero-subtitle {
            font-size: 1.05rem;
            line-height: 1.8;
            color: var(--muted);
            max-width: 480px;
            margin-bottom: 40px;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-primary {
            padding: 16px 36px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 8px 24px rgba(196, 113, 75, .3);
        }

        .btn-primary:hover {
            background: var(--terra-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(196, 113, 75, .4);
        }

        .btn-outline {
            padding: 16px 32px;
            background: transparent;
            color: var(--brown);
            border: 1.5px solid var(--sand);
            border-radius: 100px;
            font-size: .95rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: border-color .2s, background .2s;
        }

        .btn-outline:hover {
            border-color: var(--terracotta);
            background: rgba(196, 113, 75, .05);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 56px;
            padding-top: 40px;
            border-top: 1px solid var(--sand);
        }

        .hero-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--brown);
            line-height: 1;
        }

        .hero-stat-label {
            font-size: .8rem;
            color: var(--muted);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ─── SECTION BASE ─── */
        section {
            padding: 100px 40px;
        }

        .section-inner {
            max-width: 1160px;
            margin: 0 auto;
        }

        .section-eyebrow {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--terracotta);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-eyebrow::after {
            content: '';
            height: 1px;
            width: 48px;
            background: var(--terracotta);
            opacity: .5;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3.5vw, 3rem);
            line-height: 1.2;
            color: var(--brown);
            margin-bottom: 16px;
        }

        .section-sub {
            font-size: 1rem;
            color: var(--muted);
            line-height: 1.8;
            max-width: 520px;
        }

        @media (max-width: 640px) {
            section {
                padding: 70px 20px;
            }
        }

        /* ─── SERVICES ─── */
        #layanan {
            background: var(--white);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 56px;
        }

        .service-card {
            background: var(--cream);
            border: 1px solid var(--sand);
            border-radius: 20px;
            padding: 36px 32px;
            position: relative;
            overflow: hidden;
            transition: transform .3s, box-shadow .3s;
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(107, 66, 38, .1);
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--terracotta), var(--sand));
            opacity: 0;
            transition: opacity .3s;
        }

        .service-card:hover::before {
            opacity: 1;
        }

        .service-icon {
            width: 52px;
            height: 52px;
            background: rgba(196, 113, 75, .1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 24px;
        }

        .service-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--brown);
            margin-bottom: 10px;
        }

        .service-desc {
            font-size: .875rem;
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .service-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--terracotta);
        }

        .service-duration {
            font-size: .75rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* ─── THERAPISTS ─── */
        #terapis {
            background: var(--warm);
        }

        .therapists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 56px;
        }

        .therapist-card {
            background: var(--white);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            border: 1px solid var(--sand);
            transition: transform .3s, box-shadow .3s;
        }

        .therapist-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(107, 66, 38, .12);
        }

        .therapist-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--sand), var(--terracotta));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin: 0 auto 20px;
        }

        .therapist-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--brown);
            margin-bottom: 6px;
        }

        .therapist-spec {
            font-size: .8rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .therapist-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 12px;
            background: rgba(196, 113, 75, .1);
            color: var(--terracotta);
            border-radius: 100px;
            font-size: .7rem;
            font-weight: 600;
        }

        /* ─── WHY US ─── */
        #tentang {
            background: var(--white);
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 56px;
        }

        .why-card {
            padding: 32px 24px;
            border-radius: 20px;
            background: var(--cream);
            border: 1px solid var(--sand);
            text-align: center;
            transition: transform .3s, box-shadow .3s;
        }

        .why-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(107, 66, 38, .08);
        }

        .why-icon {
            font-size: 2rem;
            margin-bottom: 16px;
        }

        .why-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--brown);
            margin-bottom: 8px;
        }

        .why-text {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.7;
        }

        /* ─── BOOKING FORM ─── */
        #booking {
            background: linear-gradient(180deg, var(--cream) 0%, var(--warm) 100%);
        }

        .booking-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .booking-wrapper {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .booking-features {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 40px;
        }

        .booking-feature {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .booking-feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(196, 113, 75, .1);
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .booking-feature-title {
            font-weight: 600;
            color: var(--brown);
            font-size: .9rem;
            margin-bottom: 4px;
        }

        .booking-feature-desc {
            font-size: .8rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .booking-form-card {
            background: var(--white);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid var(--sand);
            box-shadow: 0 24px 64px rgba(107, 66, 38, .08);
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--brown);
            margin-bottom: 8px;
        }

        .form-sub {
            font-size: .85rem;
            color: var(--muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 13px 18px;
            background: var(--cream);
            border: 1.5px solid var(--sand);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control:focus {
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(196, 113, 75, .1);
            background: var(--white);
        }

        .form-control::placeholder {
            color: #c0a898;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .2s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: var(--terra-dark);
            transform: translateY(-1px);
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--brown);
            color: rgba(255, 255, 255, .7);
            padding: 60px 40px 32px;
        }

        .footer-inner {
            max-width: 1160px;
            margin: 0 auto;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
        }

        .footer-brand span {
            color: var(--sand);
            font-style: italic;
        }

        .footer-tagline {
            font-size: .85rem;
            max-width: 260px;
            line-height: 1.7;
        }

        .footer-links h4 {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: white;
            margin-bottom: 16px;
        }

        .footer-links ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            font-size: .875rem;
            color: rgba(255, 255, 255, .6);
            text-decoration: none;
            transition: color .2s;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            padding-top: 28px;
            font-size: .8rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* ─── ANIMATIONS ─── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar" id="mainNav">
        <a href="#hero" class="navbar-logo">Koichi<span>Spa</span></a>
        <ul class="navbar-links">
            <li><a href="#layanan">Layanan</a></li>
            <li><a href="#terapis">Terapis</a></li>
            <li><a href="#tentang">Tentang</a></li>
            <li><a href="#booking">Booking</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="navbar-cta">Dashboard →</a></li>
            @else
                <li><a href="{{ route('login') }}" class="navbar-cta">Masuk</a></li>
            @endauth
        </ul>
        <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </nav>

    {{-- MOBILE MENU --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="#layanan" onclick="closeMenu()">Layanan</a>
        <a href="#terapis" onclick="closeMenu()">Terapis</a>
        <a href="#tentang" onclick="closeMenu()">Tentang</a>
        <a href="#booking" onclick="closeMenu()">Booking</a>
        @auth
            <a href="{{ route('dashboard') }}" class="mobile-cta" onclick="closeMenu()">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="mobile-cta">Masuk / Daftar</a>
        @endauth
    </div>

    {{-- HERO --}}
    <section id="hero">
        <div class="hero-bg"></div>
        <div class="hero-ornament"></div>
        <div class="hero-content">
            <div class="hero-badge">Buka Setiap Hari · 09.00 – 21.00</div>
            <h1 class="hero-title">
                Temukan <em>Kedamaian</em><br>
                di Tengah Kesibukan
            </h1>
            <p class="hero-subtitle">
                Layanan spa & terapi profesional untuk memulihkan tubuh, pikiran, dan jiwa Anda.
                Dipercaya lebih dari 500 pelanggan setia.
            </p>
            <div class="hero-actions">
                <a href="#booking" class="btn-primary">
                    Booking Sekarang
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="#layanan" class="btn-outline">Lihat Layanan</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">500+</div>
                    <div class="hero-stat-label">Pelanggan Puas</div>
                </div>
                <div>
                    <div class="hero-stat-num">15+</div>
                    <div class="hero-stat-label">Terapis Bersertifikat</div>
                </div>
                <div>
                    <div class="hero-stat-num">8+</div>
                    <div class="hero-stat-label">Jenis Layanan</div>
                </div>
            </div>
        </div>
    </section>

    {{-- LAYANAN --}}
    <section id="layanan">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Layanan Kami</div>
                <h2 class="section-title">Pilihan Terapi<br>Terbaik untuk Anda</h2>
                <p class="section-sub">Setiap layanan dirancang oleh terapis bersertifikat menggunakan teknik terbaik
                    dan bahan alami pilihan.</p>
            </div>
            <div class="services-grid">
                @forelse ($services ?? [] as $service)
                    <div class="service-card fade-up">
                        <div class="service-icon">💆</div>
                        <div class="service-name">{{ $service->name }}</div>
                        <div class="service-desc">
                            {{ $service->description ?? 'Layanan profesional untuk relaksasi dan pemulihan tubuh Anda.' }}
                        </div>
                        <div class="service-price">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                        @if ($service->duration_minutes ?? null)
                            <div class="service-duration">⏱ {{ $service->duration_minutes }} menit</div>
                        @endif
                    </div>
                @empty
                    @foreach ([['💆', 'Swedish Massage', 'Teknik pijat klasik untuk melepaskan ketegangan otot dan meningkatkan sirkulasi.', '150.000'], ['🌿', 'Aromaterapi', 'Kombinasi pijat lembut dengan minyak esensial pilihan untuk ketenangan pikiran.', '180.000'], ['🔥', 'Hot Stone Therapy', 'Batu vulkanik panas yang ditempatkan di titik-titik energi tubuh untuk relaksasi dalam.', '220.000'], ['🧖', 'Facial Spa', 'Perawatan wajah mendalam dengan teknologi modern dan bahan organik.', '200.000'], ['🦶', 'Refleksiologi', 'Pijat kaki berbasis titik refleks untuk kesehatan organ internal Anda.', '120.000'], ['✨', 'Body Scrub', 'Eksfoliasi kulit menyeluruh dengan campuran garam, madu, dan minyak alami.', '160.000']] as [$icon, $name, $desc, $price])
                        <div class="service-card fade-up">
                            <div class="service-icon">{{ $icon }}</div>
                            <div class="service-name">{{ $name }}</div>
                            <div class="service-desc">{{ $desc }}</div>
                            <div class="service-price">Rp {{ $price }}</div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- TERAPIS --}}
    <section id="terapis">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Tim Kami</div>
                <h2 class="section-title">Terapis Profesional<br>& Bersertifikat</h2>
                <p class="section-sub">Setiap terapis kami telah melewati pelatihan intensif dan memiliki sertifikasi
                    resmi.</p>
            </div>
            <div class="therapists-grid">
                @forelse ($therapists ?? [] as $therapist)
                    <div class="therapist-card fade-up">
                        <div class="therapist-avatar">{{ strtoupper(substr($therapist->name, 0, 1)) }}</div>
                        <div class="therapist-name">{{ $therapist->name }}</div>
                        <div class="therapist-spec">{{ $therapist->specialization ?? 'Terapis Profesional' }}</div>
                        <div class="therapist-badge">✓ Aktif</div>
                    </div>
                @empty
                    @foreach (['Sari Dewi', 'Anita Putri', 'Bagas Pratama', 'Maya Lestari'] as $name)
                        <div class="therapist-card fade-up">
                            <div class="therapist-avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
                            <div class="therapist-name">{{ $name }}</div>
                            <div class="therapist-spec">Terapis Profesional · 5+ Tahun</div>
                            <div class="therapist-badge">✓ Aktif</div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- TENTANG --}}
    <section id="tentang">
        <div class="section-inner">
            <div class="fade-up" style="text-align:center;max-width:600px;margin:0 auto;">
                <div class="section-eyebrow" style="justify-content:center;">Kenapa Kami</div>
                <h2 class="section-title">Pengalaman Spa yang<br>Berbeda dari yang Lain</h2>
                <p class="section-sub" style="margin:0 auto;">Kami berkomitmen memberikan pengalaman wellness terbaik
                    dengan standar pelayanan tertinggi.</p>
            </div>
            <div class="why-grid">
                @foreach ([['🏅', 'Terapis Bersertifikat', 'Semua terapis kami bersertifikat nasional & internasional dengan pengalaman minimal 3 tahun.'], ['🌿', 'Bahan Alami Premium', 'Kami hanya menggunakan produk organik berkualitas tinggi yang aman untuk kulit Anda.'], ['📅', 'Booking Mudah', 'Pesan layanan kapan saja, di mana saja — tanpa perlu daftar akun terlebih dahulu.'], ['💆', 'Privasi Terjaga', 'Ruangan terapi privat yang tenang dan nyaman untuk pengalaman terbaik Anda.'], ['⏰', 'Fleksibel', 'Tersedia dari pukul 09.00–21.00 setiap hari, termasuk akhir pekan dan hari libur.'], ['💎', 'Harga Transparan', 'Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga yang Anda bayar.']] as [$icon, $title, $text])
                    <div class="why-card fade-up">
                        <div class="why-icon">{{ $icon }}</div>
                        <div class="why-title">{{ $title }}</div>
                        <div class="why-text">{{ $text }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- BOOKING --}}
    <section id="booking">
        <div class="section-inner">
            <div class="booking-wrapper">
                <div class="booking-info fade-up">
                    <div class="section-eyebrow">Reservasi Online</div>
                    <h2 class="section-title">Booking Tanpa<br>Perlu Daftar Akun</h2>
                    <p class="section-sub">Cukup isi formulir di samping dan tim kami akan mengkonfirmasi jadwal Anda
                        via WhatsApp dalam 30 menit.</p>
                    <div class="booking-features">
                        <div class="booking-feature">
                            <div class="booking-feature-icon">⚡</div>
                            <div>
                                <div class="booking-feature-title">Konfirmasi Cepat</div>
                                <div class="booking-feature-desc">Tim kami menghubungi Anda dalam 30 menit setelah
                                    booking diterima.</div>
                            </div>
                        </div>
                        <div class="booking-feature">
                            <div class="booking-feature-icon">🔒</div>
                            <div>
                                <div class="booking-feature-title">Data Aman</div>
                                <div class="booking-feature-desc">Informasi Anda hanya digunakan untuk keperluan
                                    konfirmasi booking.</div>
                            </div>
                        </div>
                        <div class="booking-feature">
                            <div class="booking-feature-icon">🔄</div>
                            <div>
                                <div class="booking-feature-title">Reschedule Gratis</div>
                                <div class="booking-feature-desc">Ubah jadwal maksimal H-1 sebelum sesi dimulai, tanpa
                                    biaya.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-form-card fade-up">
                    @if (session('booking_success'))
                        <div style="text-align:center;padding:32px 0;">
                            <div style="font-size:3.5rem;margin-bottom:20px;">✅</div>
                            <h3
                                style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--brown);margin-bottom:12px;">
                                Booking Berhasil!
                            </h3>
                            <p style="color:var(--muted);font-size:.9rem;line-height:1.8;">
                                Terima kasih! Tim kami akan segera menghubungi Anda via WhatsApp untuk konfirmasi
                                jadwal.
                            </p>
                            <a href="{{ route('welcome') }}"
                                style="display:inline-block;margin-top:24px;padding:12px 28px;background:var(--terracotta);color:white;border-radius:100px;font-weight:600;font-size:.875rem;">
                                Buat Booking Lain
                            </a>
                        </div>
                    @else
                        <h3 class="form-title">Buat Reservasi</h3>
                        <p class="form-sub">Isi data di bawah ini. Anda tidak perlu membuat akun.</p>

                        @if ($errors->any())
                            <div
                                style="margin-bottom:20px;padding:12px 16px;background:#fff5f5;border:1px solid #feb2b2;border-radius:10px;font-size:.85rem;color:#c53030;">
                                @foreach ($errors->all() as $error)
                                    <div style="margin-bottom:4px;">⚠ {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.booking.store') }}">
                            @csrf
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Nama Anda" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">No. WhatsApp *</label>
                                    <input type="tel" name="phone" class="form-control"
                                        placeholder="08xx-xxxx-xxxx" value="{{ old('phone') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Layanan *</label>
                                <select name="service_id" class="form-control" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    @forelse ($services ?? [] as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} — Rp
                                            {{ number_format($service->price, 0, ',', '.') }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Belum ada layanan tersedia</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pilih Terapis (Opsional)</label>
                                <select name="therapist_id" class="form-control">
                                    <option value="">-- Terapis Mana Saja --</option>
                                    @foreach ($therapists ?? [] as $therapist)
                                        <option value="{{ $therapist->id }}"
                                            {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                                            {{ $therapist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal & Waktu *</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control"
                                    value="{{ old('scheduled_at') }}" required
                                    min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Keluhan khusus, permintaan tertentu...">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" class="submit-btn">Kirim Reservasi →</button>

                            <p
                                style="text-align:center;font-size:.75rem;color:var(--muted);margin-top:16px;line-height:1.6;">
                                Sudah punya akun?
                                <a href="{{ route('login') }}"
                                    style="color:var(--terracotta);font-weight:600;text-decoration:none;">Masuk di
                                    sini</a>
                                untuk mengelola booking Anda.
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">Koichi<span>Spa</span></div>
                    <div class="footer-tagline">Wellness & Terapi Profesional. Hadir untuk memulihkan keseimbangan
                        tubuh dan pikiran Anda.</div>
                </div>
                <div class="footer-links">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#terapis">Terapis</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#booking">Booking</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Info</h4>
                    <ul>
                        <li><a href="#">Jam Operasional</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('login') }}">Login Admin</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Kontak</h4>
                    <ul>
                        <li><a href="#">📍 Jl. Melati Raya No. 47, Cirebon</a></li>
                        <li><a href="#">📞 0821-5567-3894</a></li>
                        <li><a href="#">✉ hello@koichispa.id</a></li>
                        <li><a href="#">⏰ 09.00 – 21.00</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© {{ date('Y') }} KoichiSpa. Hak cipta dilindungi.</span>
                <span>Dibuat dengan ❤ untuk kesehatan Anda</span>
            </div>
        </div>
    </footer>

    <script>
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 30);
        }, {
            passive: true
        });

        function toggleMenu() {
            const menu = document.getElementById('mobileMenu');
            const btn = document.getElementById('hamburger');
            const isOpen = menu.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen);
        }

        function closeMenu() {
            document.getElementById('mobileMenu').classList.remove('open');
            document.getElementById('hamburger').setAttribute('aria-expanded', 'false');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('mobileMenu');
            const btn = document.getElementById('hamburger');
            if (menu.classList.contains('open') &&
                !menu.contains(e.target) &&
                !btn.contains(e.target)) {
                closeMenu();
            }
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, i * 80);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (!href || href === '#') return;
                const target = document.querySelector(href);
                if (!target) return;
                e.preventDefault();
                const top = target.getBoundingClientRect().top + window.scrollY - 80;
                window.scrollTo({
                    top,
                    behavior: 'smooth'
                });
            });
        });

        @if (session('booking_success'))
            window.addEventListener('load', () => {
                const el = document.getElementById('booking');
                if (el) {
                    setTimeout(() => {
                        window.scrollTo({
                            top: el.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }, 300);
                }
            });
        @endif
    </script>

</body>

</html>
