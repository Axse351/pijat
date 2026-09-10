<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Koichi Pijat Refleksi — Wellness & Terapi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400&family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --cream: #f4f1e6;
            --warm: #eaeddc;
            --sand: #d7ddc4;
            --terracotta: #b8874f;
            --terra-dark: #96692f;
            --brown: #1e3a2c;
            --text: #1c2118;
            --muted: #6e7566;
            --white: #ffffff;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* ── NAVBAR ── */
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
            background: rgba(244, 241, 230, .96);
            backdrop-filter: blur(14px);
            box-shadow: 0 2px 20px rgba(30, 58, 44, .08);
        }

        .navbar-logo {
            font-family: 'Fraunces', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brown);
            letter-spacing: -.02em;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-logo-img {
            height: 50px;
            width: auto;
            object-fit: contain;
            display: block;
            transition: transform .3s ease;
        }

        .navbar-logo:hover .navbar-logo-img {
            transform: scale(1.05);
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
            z-index: 99;
            box-shadow: 0 8px 32px rgba(30, 58, 44, .12);
        }

        .mobile-menu.open {
            display: flex !important;
        }

        .mobile-menu a {
            color: var(--brown) !important;
            font-size: 1rem;
            font-weight: 500;
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

        @media(max-width:768px) {
            .navbar {
                padding: 0 20px;
            }

            .navbar-logo-img {
                height: 40px;
            }

            .navbar-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }
        }

        /* ── HERO (dengan slider foto background) ── */
        #hero {
            position: relative;
            background: linear-gradient(160deg, var(--brown) 0%, #16281d 100%);
            overflow: hidden;
            padding: 0;
            min-height: 640px;
            display: flex;
            align-items: center;
        }

        /* Slider background di belakang konten hero */
        .hero-slider-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1.2s ease;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-slide-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.08);
            transition: transform 9s ease;
        }

        .hero-slide.active .hero-slide-img {
            transform: scale(1);
        }

        .hero-slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(30, 58, 44, .88) 0%, rgba(22, 40, 29, .82) 100%);
        }

        .hero-slider-bg .promo-placeholder-text {
            color: rgba(244, 241, 230, .5);
        }

        .hero-dots {
            position: absolute;
            bottom: 24px;
            left: 0;
            right: 0;
            z-index: 5;
            display: flex;
            justify-content: center;
            gap: 7px;
        }

        .hero-dots .promo-dot {
            background: rgba(244, 241, 230, .35);
        }

        .hero-dots .promo-dot.active {
            background: #e3bd80;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 760px;
            margin: 0 auto;
            text-align: center;
            color: var(--white);
            padding: 170px 40px 100px;
            width: 100%;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(244, 241, 230, .12);
            border: 1px solid rgba(244, 241, 230, .35);
            backdrop-filter: blur(6px);
            border-radius: 100px;
            font-size: .75rem;
            font-weight: 600;
            color: var(--white);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #d9b877;
            border-radius: 50%;
            animation: blink 2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .4;
                transform: scale(.75)
            }
        }

        .hero-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(2.2rem, 4.4vw, 3.8rem);
            line-height: 1.1;
            color: var(--white);
            margin-bottom: 20px;
            text-wrap: balance;
        }

        .hero-title em {
            font-style: italic;
            color: #e3bd80;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            line-height: 1.8;
            color: rgba(244, 241, 230, .82);
            max-width: 520px;
            margin: 0 auto 36px;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 44px;
        }

        .btn-primary {
            padding: 16px 36px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 100px;
            font-family: 'Public Sans', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .28);
        }

        .btn-primary:hover {
            background: var(--terra-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .32);
        }

        .btn-outline {
            padding: 16px 32px;
            background: rgba(255, 255, 255, .06);
            color: var(--white);
            border: 1.5px solid rgba(244, 241, 230, .45);
            border-radius: 100px;
            font-size: .95rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: border-color .2s, background .2s;
        }

        .btn-outline:hover {
            border-color: var(--white);
            background: rgba(255, 255, 255, .14);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            justify-content: center;
            padding-top: 28px;
            border-top: 1px solid rgba(244, 241, 230, .25);
        }

        .hero-stat-num {
            font-family: 'Fraunces', serif;
            font-size: 1.9rem;
            font-weight: 600;
            color: var(--white);
            line-height: 1;
        }

        .hero-stat-label {
            font-size: .78rem;
            color: rgba(244, 241, 230, .72);
            margin-top: 4px;
            font-weight: 500;
        }

        @media(max-width:640px) {
            .hero-inner {
                padding: 130px 20px 70px;
            }

            .hero-stats {
                gap: 24px;
            }
        }

        /* ── SECTIONS ── */
        section {
            padding: 100px 40px;
        }

        .section-inner {
            max-width: 1160px;
            margin: 0 auto;
        }

        .section-eyebrow {
            font-size: .8rem;
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
            font-family: 'Fraunces', serif;
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

        @media(max-width:640px) {
            section {
                padding: 70px 20px;
            }
        }

        /* ── LAYANAN ── */
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
            box-shadow: 0 20px 48px rgba(30, 58, 44, .1);
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
            background: rgba(184, 135, 79, .12);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 24px;
        }

        .service-name {
            font-family: 'Fraunces', serif;
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

        /* ── PROMO ── */
        #promo {
            background: var(--warm);
        }

        .promo-slider-wrap {
            position: relative;
            margin-top: 56px;
            width: 100%;
            height: min(480px, 62vw);
            max-height: 520px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(30, 58, 44, .16);
            background: #16281d;
        }

        @media(max-width:640px) {
            .promo-slider-wrap {
                height: 70vw;
                border-radius: 16px;
            }
        }

        .promo-slides-track {
            display: flex;
            height: 100%;
            transition: transform .65s cubic-bezier(.77, 0, .175, 1);
            will-change: transform;
        }

        .promo-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            flex-shrink: 0;
        }

        .promo-slide-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 8s ease;
        }

        .promo-slide.active .promo-slide-img {
            transform: scale(1.06);
        }

        .promo-slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(20, 32, 22, 0) 40%, rgba(20, 32, 22, .35) 70%, rgba(20, 32, 22, .68) 100%);
        }

        .promo-slide-body {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            padding: 28px 28px 64px;
            color: var(--white);
        }

        .promo-slide-badge {
            display: inline-block;
            padding: 5px 14px;
            background: var(--terracotta);
            border-radius: 100px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .promo-slide-desc {
            font-size: .9rem;
            line-height: 1.7;
            max-width: 460px;
            color: rgba(244, 241, 230, .9);
        }

        .promo-nav {
            position: absolute;
            top: 16px;
            right: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
        }

        .promo-nav-btn {
            width: 36px;
            height: 36px;
            background: rgba(244, 241, 230, .14);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(244, 241, 230, .3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .promo-nav-btn:hover {
            background: rgba(244, 241, 230, .3);
            transform: scale(1.1);
        }

        .promo-counter {
            font-size: .7rem;
            color: rgba(244, 241, 230, .85);
            font-weight: 600;
            letter-spacing: .05em;
            background: rgba(20, 32, 22, .3);
            backdrop-filter: blur(6px);
            padding: 3px 10px;
            border-radius: 100px;
            border: 1px solid rgba(244, 241, 230, .18);
        }

        .promo-dots {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            z-index: 10;
        }

        .promo-dot {
            width: 22px;
            height: 3px;
            border-radius: 100px;
            background: rgba(244, 241, 230, .4);
            cursor: pointer;
            transition: all .35s;
        }

        .promo-dot.active {
            background: #e3bd80;
            width: 34px;
        }

        .promo-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .promo-placeholder-icon {
            font-size: 44px;
            opacity: .25;
        }

        .promo-placeholder-text {
            font-size: 10px;
            color: rgba(255, 255, 255, .4);
            text-align: center;
            padding: 0 16px;
            line-height: 1.7;
            font-family: 'Public Sans', sans-serif;
        }

        /* ── JADWAL (versi simpel: jam operasional + terapis) ── */
        #jadwal {
            background: var(--white);
        }

        .jd-hours-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 40px;
            margin-bottom: 48px;
        }

        .jd-hours-card {
            flex: 1;
            min-width: 180px;
            background: var(--cream);
            border: 1.5px solid var(--sand);
            border-radius: 16px;
            padding: 22px 20px;
            text-align: center;
        }

        .jd-hours-num {
            font-family: 'Fraunces', serif;
            font-size: 1.6rem;
            color: var(--brown);
        }

        .jd-hours-label {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .therapists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 24px;
        }

        .therapist-card {
            background: var(--cream);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            border: 1px solid var(--sand);
            transition: transform .3s, box-shadow .3s;
        }

        .therapist-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(30, 58, 44, .12);
        }

        .therapist-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--sand), var(--terracotta));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fraunces', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            margin: 0 auto 18px;
        }

        .therapist-name {
            font-family: 'Fraunces', serif;
            font-size: 1.05rem;
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
            background: rgba(184, 135, 79, .12);
            color: var(--terracotta);
            border-radius: 100px;
            font-size: .7rem;
            font-weight: 600;
        }

        .jd-note {
            margin-top: 32px;
            padding: 14px 18px;
            background: rgba(184, 135, 79, .08);
            border-left: 3px solid var(--terracotta);
            border-radius: 0 10px 10px 0;
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.8;
        }

        .jd-note a {
            color: var(--terracotta);
            font-weight: 600;
        }

        .jd-empty {
            padding: 40px 24px;
            text-align: center;
            color: var(--muted);
            background: var(--cream);
            border: 1.5px dashed var(--sand);
            border-radius: 20px;
            font-size: .9rem;
        }

        /* ── BOOKING ── */
        #booking {
            background: linear-gradient(180deg, var(--cream) 0%, var(--warm) 100%);
        }

        .booking-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
        }

        @media(max-width:900px) {
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
            background: rgba(184, 135, 79, .12);
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
            box-shadow: 0 24px 64px rgba(30, 58, 44, .08);
        }

        .form-title {
            font-family: 'Fraunces', serif;
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
            font-family: 'Public Sans', sans-serif;
            font-size: .9rem;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control:focus {
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(184, 135, 79, .12);
            background: var(--white);
        }

        .form-control::placeholder {
            color: #a89b7f;
        }

        .form-control:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media(max-width:500px) {
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
            font-family: 'Public Sans', sans-serif;
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

        .submit-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        /* ── SLOT PICKER ── */
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 4px;
        }

        .slot-btn {
            padding: 9px 4px;
            border-radius: 10px;
            font-family: 'Public Sans', sans-serif;
            font-size: .78rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            border: 1.5px solid var(--sand);
            background: var(--cream);
            color: var(--brown);
            transition: all .15s;
            line-height: 1.3;
        }

        .slot-btn:hover:not(:disabled) {
            border-color: var(--terracotta);
            background: rgba(184, 135, 79, .08);
        }

        .slot-btn.selected {
            background: var(--terracotta);
            border-color: var(--terracotta);
            color: white;
        }

        .slot-btn:disabled {
            background: #f3f4f6;
            border-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .slot-btn .slot-lock {
            display: block;
            font-size: .6rem;
            margin-bottom: 1px;
            opacity: .7;
        }

        .slot-hint {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .slot-hint-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .slot-loading {
            text-align: center;
            padding: 20px;
            color: var(--muted);
            font-size: .85rem;
        }

        /* ── LOKASI ── */
        #lokasi {
            background: var(--warm);
        }

        .lokasi-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 40px;
            margin-top: 56px;
            align-items: stretch;
        }

        @media(max-width:900px) {
            .lokasi-grid {
                grid-template-columns: 1fr;
            }
        }

        .lokasi-map {
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--sand);
            box-shadow: 0 24px 64px rgba(30, 58, 44, .1);
            min-height: 340px;
        }

        .lokasi-map iframe {
            width: 100%;
            height: 100%;
            min-height: 340px;
            display: block;
            border: 0;
        }

        .lokasi-info {
            background: var(--white);
            border-radius: 24px;
            border: 1px solid var(--sand);
            padding: 36px 32px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .lokasi-info-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .lokasi-info-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(184, 135, 79, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .lokasi-info-title {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin-bottom: 3px;
        }

        .lokasi-info-value {
            font-size: .92rem;
            color: var(--brown);
            font-weight: 500;
            line-height: 1.5;
        }

        .lokasi-wa-btn {
            margin-top: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            background: var(--terracotta);
            color: white !important;
            border-radius: 12px;
            font-weight: 600;
            font-size: .9rem;
            transition: background .2s;
        }

        .lokasi-wa-btn:hover {
            background: var(--terra-dark);
        }

        /* ── FOOTER ── */
        footer {
            background: var(--brown);
            color: rgba(244, 241, 230, .7);
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
            border-bottom: 1px solid rgba(244, 241, 230, .12);
        }

        .footer-brand {
            font-family: 'Fraunces', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
        }

        .footer-brand span {
            color: #e3bd80;
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
            color: rgba(244, 241, 230, .6);
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

        .fade-up {
            opacity: 0;
            transition: opacity .6s ease;
        }

        .fade-up.visible {
            opacity: 1;
        }
    </style>
</head>

<body>

    {{-- ── NAVBAR ── --}}
    <nav class="navbar" id="mainNav">
        <a href="#hero" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Koichi Spa" class="navbar-logo-img">
        </a>
        <ul class="navbar-links">
            <li><a href="#layanan">Layanan</a></li>
            <li><a href="#promo">Promo</a></li>
            <li><a href="#jadwal">Jadwal</a></li>
            <li><a href="#booking">Booking</a></li>
            <li><a href="#lokasi">Lokasi</a></li>
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

    {{-- ── MOBILE MENU ── --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="#layanan" onclick="closeMenu()">Layanan</a>
        <a href="#promo" onclick="closeMenu()">Promo</a>
        <a href="#jadwal" onclick="closeMenu()">Jadwal</a>
        <a href="#booking" onclick="closeMenu()">Booking</a>
        <a href="#lokasi" onclick="closeMenu()">Lokasi</a>
        @auth
            <a href="{{ route('dashboard') }}" class="mobile-cta" onclick="closeMenu()">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="mobile-cta">Masuk / Daftar</a>
        @endauth
    </div>

    {{-- ── HERO (dengan slider foto background) ── --}}
    <section id="hero">

        {{-- Slider background --}}
        <div class="hero-slider-bg" id="heroSlider">
            @php
                // Taruh foto pijat/spa di public/images/hero/1.jpg, 2.jpg, dst.
                // Foto gratis bisa didownload dari unsplash.com/s/photos/massage-spa
                // atau pexels.com/search/spa%20massage — selama file belum ada,
                // otomatis tampil placeholder gradient (sama seperti section Promo).
                $heroSlides = ['1.jpg', '2.jpg', '3.jpg', '4.jpg'];
                $heroColors = ['#1e3a2c', '#2d5240', '#3c6b54', '#254732'];
            @endphp
            @foreach ($heroSlides as $i => $file)
                @php $heroExists = file_exists(public_path('images/hero/' . $file)); @endphp
                <div class="hero-slide {{ $i === 0 ? 'active' : '' }}">
                    @if ($heroExists)
                        <img class="hero-slide-img" src="{{ asset('images/hero/' . $file) }}"
                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}" alt="Suasana Koichi Pijat Refleksi">
                    @else
                        <div class="promo-placeholder"
                            style="background:linear-gradient(145deg,{{ $heroColors[$i % count($heroColors)] }},{{ $heroColors[$i % count($heroColors)] }}99);">
                            <div class="promo-placeholder-icon">🖼</div>
                            <div class="promo-placeholder-text">Taruh foto di:<br>
                                <strong style="opacity:.6;">public/images/hero/{{ $file }}</strong>
                            </div>
                        </div>
                    @endif
                    <div class="hero-slide-overlay"></div>
                </div>
            @endforeach
            <div class="hero-dots" id="heroDots"></div>
        </div>

        <div class="hero-inner">
            <div class="hero-badge">{{ $content['hero_badge'] ?? 'Buka Setiap Hari · 09.00 – 20.00' }}</div>
            <h1 class="hero-title">
                {{ $content['hero_title_plain'] ?? 'Temukan' }}
                <em>{{ $content['hero_title_italic'] ?? 'Kedamaian' }}</em><br>
                {{ $content['hero_title_line2'] ?? 'di Tengah Kesibukan' }}
            </h1>
            <p class="hero-subtitle">
                {{ $content['hero_subtitle'] ?? 'Layanan spa & terapi profesional untuk memulihkan tubuh, pikiran, dan jiwa Anda. Dipercaya lebih dari 500 pelanggan setia.' }}
            </p>
            <div class="hero-actions">
                <a href="#booking" class="btn-primary">
                    {{ $content['hero_btn_primary'] ?? 'Booking Sekarang' }}
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="#layanan" class="btn-outline">{{ $content['hero_btn_secondary'] ?? 'Lihat Layanan' }}</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">{{ $content['stat_1_num'] ?? '500+' }}</div>
                    <div class="hero-stat-label">{{ $content['stat_1_label'] ?? 'Pelanggan Puas' }}</div>
                </div>
                <div>
                    <div class="hero-stat-num">{{ $content['stat_2_num'] ?? '15+' }}</div>
                    <div class="hero-stat-label">{{ $content['stat_2_label'] ?? 'Terapis Bersertifikat' }}</div>
                </div>
                <div>
                    <div class="hero-stat-num">{{ $content['stat_3_num'] ?? '8+' }}</div>
                    <div class="hero-stat-label">{{ $content['stat_3_label'] ?? 'Jenis Layanan' }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── LAYANAN ── --}}
    <section id="layanan">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">{{ $content['layanan_eyebrow'] ?? 'Layanan Kami' }}</div>
                <h2 class="section-title">
                    {{ $content['layanan_title_1'] ?? 'Pilihan Terapi' }}<br>
                    {{ $content['layanan_title_2'] ?? 'Terbaik untuk Anda' }}
                </h2>
                <p class="section-sub">
                    {{ $content['layanan_sub'] ?? 'Setiap layanan dirancang oleh terapis bersertifikat menggunakan teknik terbaik dan bahan alami pilihan.' }}
                </p>
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

    {{-- ── PROMO (foto-foto promo dari slider hero, sekarang jadi section sendiri) ── --}}
    <section id="promo">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">{{ $content['promo_eyebrow'] ?? 'Promo Spesial' }}</div>
                <h2 class="section-title">
                    {{ $content['promo_title_1'] ?? 'Penawaran Menarik' }}<br>
                    {{ $content['promo_title_2'] ?? 'Untuk Anda' }}
                </h2>
                <p class="section-sub">
                    {{ $content['promo_sub'] ?? 'Cek promo dan paket membership eksklusif kami yang selalu diperbarui.' }}
                </p>
            </div>

            <div class="promo-slider-wrap fade-up" id="promoSlider">
                <div class="promo-slides-track" id="slidesTrack">
                    @php
                        $slides = [
                            [
                                'file' => '1.jpeg',
                                'badge' => 'Buy 4 Get 1',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '2.jpeg',
                                'badge' => 'Weekend Deal',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '3.jpeg',
                                'badge' => 'Hot Deal',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '4.jpeg',
                                'badge' => 'Facial',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '5.jpeg',
                                'badge' => 'Facial',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '6.jpeg',
                                'badge' => 'Facial',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                            [
                                'file' => '7.jpeg',
                                'badge' => 'Facial',
                                'desc' =>
                                    'Pilihan paket membership eksklusif KOICHI Family Reflexology dengan penawaran harga terbaik.',
                            ],
                        ];
                        $placeholderColors = ['#1e3a2c', '#2d5240', '#3c6b54', '#254732', '#345f47'];
                    @endphp
                    @foreach ($slides as $i => $slide)
                        @php
                            $exists = file_exists(public_path('images/promos/' . $slide['file']));
                            $color = $placeholderColors[$i % count($placeholderColors)];
                        @endphp
                        <div class="promo-slide {{ $i === 0 ? 'active' : '' }}">
                            @if ($exists)
                                <img class="promo-slide-img" src="{{ asset('images/promos/' . $slide['file']) }}"
                                    loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                            @else
                                <div class="promo-placeholder"
                                    style="background:linear-gradient(145deg,{{ $color }},{{ $color }}99);">
                                    <div class="promo-placeholder-icon">🖼</div>
                                    <div class="promo-placeholder-text">Taruh foto di:<br><strong
                                            style="opacity:.6;">public/images/promos/{{ $slide['file'] }}</strong>
                                    </div>
                                </div>
                            @endif
                            <div class="promo-slide-overlay"></div>
                            <div class="promo-slide-body">
                                @if ($slide['badge'])
                                    <div class="promo-slide-badge">{{ $slide['badge'] }}</div>
                                @endif
                                @if ($slide['desc'])
                                    <div class="promo-slide-desc">{{ $slide['desc'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="promo-nav">
                    <button class="promo-nav-btn" id="promoPrev" aria-label="Sebelumnya">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M9 2L4 7l5 5" stroke="white" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="promo-counter" id="promoCounter">1 / {{ count($slides) }}</div>
                    <button class="promo-nav-btn" id="promoNext" aria-label="Berikutnya">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M5 2l5 5-5 5" stroke="white" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="promo-dots" id="promoDots"></div>
            </div>
        </div>
    </section>

    {{-- ── JADWAL (versi simpel: jam operasional + daftar terapis, tanpa kalender) ── --}}
    <section id="jadwal">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">{{ $content['jadwal_eyebrow'] ?? 'Jam Operasional' }}</div>
                <h2 class="section-title">
                    {{ $content['jadwal_title_1'] ?? 'Kapan Kami Buka' }}<br>
                    {{ $content['jadwal_title_2'] ?? '& Siapa yang Melayani' }}
                </h2>
                <p class="section-sub">
                    {{ $content['jadwal_sub'] ?? 'Kami buka setiap hari dengan tim terapis bersertifikat yang siap melayani Anda.' }}
                </p>
            </div>

            <div class="jd-hours-row fade-up">
                <div class="jd-hours-card">
                    <div class="jd-hours-num">{{ $content['footer_hours'] ?? '09.00 – 20.00' }}</div>
                    <div class="jd-hours-label">Setiap Hari, Termasuk Akhir Pekan & Libur</div>
                </div>
                <div class="jd-hours-card">
                    <div class="jd-hours-num">{{ count($therapists ?? []) ?: '4+' }}</div>
                    <div class="jd-hours-label">Terapis Bersertifikat Siap Melayani</div>
                </div>
            </div>

            <div class="therapists-grid fade-up">
                @forelse ($therapists ?? [] as $therapist)
                    <div class="therapist-card">
                        <div class="therapist-avatar">{{ strtoupper(substr($therapist->name, 0, 1)) }}</div>
                        <div class="therapist-name">{{ $therapist->name }}</div>
                        <div class="therapist-spec">{{ $therapist->specialization ?? 'Terapis Profesional' }}</div>
                        <div class="therapist-badge">✓ Tersedia</div>
                    </div>
                @empty
                    @foreach (['Sari Dewi', 'Anita Putri', 'Bagas Pratama', 'Maya Lestari'] as $name)
                        <div class="therapist-card">
                            <div class="therapist-avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
                            <div class="therapist-name">{{ $name }}</div>
                            <div class="therapist-spec">Terapis Profesional · 5+ Tahun</div>
                            <div class="therapist-badge">✓ Tersedia</div>
                        </div>
                    @endforeach
                @endforelse
            </div>

            <p class="jd-note fade-up">
                💡 Untuk memastikan terapis pilihan Anda tersedia pada jam tertentu, silakan
                <a href="#booking">buat reservasi</a> — tim kami akan konfirmasi via WhatsApp dalam 30 menit.
            </p>
        </div>
    </section>

    {{-- ── BOOKING ── --}}
    <section id="booking">
        <div class="section-inner">
            <div class="booking-wrapper">
                <div class="booking-info fade-up">
                    <div class="section-eyebrow">{{ $content['booking_eyebrow'] ?? 'Reservasi Online' }}</div>
                    <h2 class="section-title">
                        {{ $content['booking_title_1'] ?? 'Booking Tanpa' }}<br>
                        {{ $content['booking_title_2'] ?? 'Perlu Daftar Akun' }}
                    </h2>
                    <p class="section-sub">
                        {{ $content['booking_sub'] ?? 'Cukup isi formulir di samping dan tim kami akan mengkonfirmasi jadwal Anda via WhatsApp dalam 30 menit.' }}
                    </p>
                    <div class="booking-features">
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
                        @foreach ($bookingFeatures as $feat)
                            <div class="booking-feature">
                                <div class="booking-feature-icon">
                                    {{ $content[$feat['key'] . '_icon'] ?? $feat['icon'] }}</div>
                                <div>
                                    <div class="booking-feature-title">
                                        {{ $content[$feat['key'] . '_title'] ?? $feat['title'] }}</div>
                                    <div class="booking-feature-desc">
                                        {{ $content[$feat['key'] . '_desc'] ?? $feat['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="booking-form-card fade-up">
                    @if (session('booking_success'))
                        <div style="text-align:center;padding:32px 0;">
                            <div style="font-size:3.5rem;margin-bottom:20px;">✅</div>
                            <h3
                                style="font-family:'Fraunces',serif;font-size:1.5rem;color:var(--brown);margin-bottom:12px;">
                                Booking Berhasil!</h3>
                            <p style="color:var(--muted);font-size:.9rem;line-height:1.8;">Terima kasih! Tim kami akan
                                segera menghubungi Anda via WhatsApp untuk konfirmasi jadwal.</p>
                            <a href="{{ route('welcome') }}"
                                style="display:inline-block;margin-top:24px;padding:12px 28px;background:var(--terracotta);color:white;border-radius:100px;font-weight:600;font-size:.875rem;">Buat
                                Booking Lain</a>
                        </div>
                    @else
                        <h3 class="form-title">{{ $content['booking_form_title'] ?? 'Buat Reservasi' }}</h3>
                        <p class="form-sub">
                            {{ $content['booking_form_sub'] ?? 'Isi data di bawah ini. Anda tidak perlu membuat akun.' }}
                        </p>

                        @if ($errors->any())
                            <div
                                style="margin-bottom:20px;padding:12px 16px;background:#fff5f5;border:1px solid #feb2b2;border-radius:10px;font-size:.85rem;color:#c53030;">
                                @foreach ($errors->all() as $error)
                                    <div style="margin-bottom:4px;">⚠ {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.booking.store') }}" id="bookingForm">
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
                                <select name="service_id" class="form-control" id="serviceSelect" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    @forelse($services ?? [] as $service)
                                        <option value="{{ $service->id }}"
                                            data-duration="{{ $service->duration_minutes ?? 60 }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} — Rp
                                            {{ number_format($service->price, 0, ',', '.') }}
                                            ({{ $service->duration_minutes ?? 60 }} mnt)
                                        </option>
                                    @empty
                                        <option value="" disabled>Belum ada layanan tersedia</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pilih Terapis (Opsional)</label>
                                <select name="therapist_id" class="form-control" id="bookingTherapistSelect">
                                    <option value="">-- Terapis Mana Saja --</option>
                                    @foreach ($therapists ?? [] as $therapist)
                                        <option value="{{ $therapist->id }}"
                                            {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                                            {{ $therapist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div class="form-group">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" id="bookingDate" name="booking_date" class="form-control"
                                    min="{{ now()->addHour()->format('Y-m-d') }}"
                                    value="{{ old('booking_date', now()->addHour()->format('Y-m-d')) }}" required>
                            </div>

                            {{-- Slot Jam --}}
                            <div class="form-group">
                                <label class="form-label">Pilih Jam *</label>
                                <div id="slotArea">
                                    <div class="slot-loading">Pilih terapis dan tanggal untuk melihat ketersediaan jam.
                                    </div>
                                </div>
                                {{-- hidden input yang dikirim ke server --}}
                                <input type="hidden" name="scheduled_at" id="scheduledAt"
                                    value="{{ old('scheduled_at') }}">
                                <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap;">
                                    <div class="slot-hint">
                                        <div class="slot-hint-dot" style="background:#22c55e;"></div> Tersedia
                                    </div>
                                    <div class="slot-hint">
                                        <div class="slot-hint-dot" style="background:#9ca3af;"></div> Sudah dipesan
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Keluhan khusus, permintaan tertentu...">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                                Pilih jam terlebih dahulu
                            </button>
                            <p
                                style="text-align:center;font-size:.75rem;color:var(--muted);margin-top:16px;line-height:1.6;">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" style="color:var(--terracotta);font-weight:600;">Masuk
                                    di sini</a>
                                untuk mengelola booking Anda.
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ── LOKASI ── --}}
    <section id="lokasi">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">{{ $content['lokasi_eyebrow'] ?? 'Temukan Kami' }}</div>
                <h2 class="section-title">
                    {{ $content['lokasi_title_1'] ?? 'Lokasi &' }}<br>
                    {{ $content['lokasi_title_2'] ?? 'Kontak Kami' }}
                </h2>
                <p class="section-sub">
                    {{ $content['lokasi_sub'] ?? 'Kunjungi langsung outlet kami atau hubungi tim kami untuk informasi lebih lanjut.' }}
                </p>
            </div>

            @php
                $waNumberRaw = preg_replace('/\D/', '', $content['footer_phone'] ?? '0821-5567-3894');
                $waNumber = '62' . ltrim($waNumberRaw, '0');
            @endphp

            <div class="lokasi-grid fade-up">
                <div class="lokasi-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4700.297788256569!2d108.5652088!3d-6.7098533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee30073f1dad5%3A0x8560f853c9845edd!2sKOICHI%20Family%20Reflexology%20Cirebon!5e1!3m2!1sid!2sid!4v1788575118879!5m2!1sid!2sid"
                        allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
                <div class="lokasi-info">
                    <div class="lokasi-info-item">
                        <div class="lokasi-info-icon">📍</div>
                        <div>
                            <div class="lokasi-info-title">Alamat</div>
                            <div class="lokasi-info-value">
                                {{ $content['footer_address'] ?? 'Jl. Melati Raya No. 47, Cirebon' }}</div>
                        </div>
                    </div>
                    <div class="lokasi-info-item">
                        <div class="lokasi-info-icon">⏰</div>
                        <div>
                            <div class="lokasi-info-title">Jam Buka</div>
                            <div class="lokasi-info-value">{{ $content['footer_hours'] ?? '09.00 – 20.00' }}, Setiap
                                Hari</div>
                        </div>
                    </div>
                    <div class="lokasi-info-item">
                        <div class="lokasi-info-icon">📞</div>
                        <div>
                            <div class="lokasi-info-title">Telepon / WhatsApp</div>
                            <div class="lokasi-info-value">{{ $content['footer_phone'] ?? '0821-5567-3894' }}</div>
                        </div>
                    </div>
                    <div class="lokasi-info-item">
                        <div class="lokasi-info-icon">✉</div>
                        <div>
                            <div class="lokasi-info-title">Email</div>
                            <div class="lokasi-info-value">{{ $content['footer_email'] ?? 'hello@koichispa.id' }}
                            </div>
                        </div>
                    </div>
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                        class="lokasi-wa-btn">
                        💬 Chat via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">
                        {{ $content['footer_brand'] ?? 'Koichi' }}<span>{{ $content['footer_brand_accent'] ?? 'Spa' }}</span>
                    </div>
                    <div class="footer-tagline">
                        {{ $content['footer_tagline'] ?? 'Wellness & Terapi Profesional. Hadir untuk memulihkan keseimbangan tubuh dan pikiran Anda.' }}
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#promo">Promo</a></li>
                        <li><a href="#jadwal">Jadwal</a></li>
                        <li><a href="#booking">Booking</a></li>
                        <li><a href="#lokasi">Lokasi</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Info</h4>
                    <ul>
                        <li><a href="#jadwal">Jam Operasional</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('login') }}">Login Admin</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Kontak</h4>
                    <ul>
                        <li><a href="#lokasi">📍
                                {{ $content['footer_address'] ?? 'Jl. Melati Raya No. 47, Cirebon' }}</a></li>
                        <li><a href="#lokasi">📞 {{ $content['footer_phone'] ?? '0821-5567-3894' }}</a></li>
                        <li><a href="#lokasi">✉ {{ $content['footer_email'] ?? 'hello@koichispa.id' }}</a></li>
                        <li><a href="#jadwal">⏰ {{ $content['footer_hours'] ?? '09.00 – 20.00' }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© {{ date('Y') }} KoichiSpa. Hak cipta dilindungi.</span>
                <span>{{ $content['footer_copyright'] ?? 'Dibuat dengan ❤ untuk kesehatan Anda' }}</span>
            </div>
        </div>
    </footer>

    <script>
        /* ── NAVBAR ── */
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 30), {
            passive: true
        });

        function toggleMenu() {
            const menu = document.getElementById('mobileMenu'),
                btn = document.getElementById('hamburger');
            btn.setAttribute('aria-expanded', menu.classList.toggle('open'));
        }

        function closeMenu() {
            document.getElementById('mobileMenu').classList.remove('open');
            document.getElementById('hamburger').setAttribute('aria-expanded', 'false');
        }
        document.addEventListener('click', e => {
            const menu = document.getElementById('mobileMenu'),
                btn = document.getElementById('hamburger');
            if (menu.classList.contains('open') && !menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
        });

        /* ── FADE-UP ── */
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08
        });
        document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));

        /* ── SMOOTH SCROLL ── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const href = a.getAttribute('href');
                if (!href || href.length < 2) return;
                let target;
                try {
                    target = document.querySelector(href);
                } catch (_) {
                    return;
                }
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        /* ── HERO SLIDER (autoplay fade, cuma dot — dekorasi background) ── */
        (function() {
            const wrap = document.getElementById('heroSlider');
            if (!wrap) return;
            const slides = Array.from(wrap.querySelectorAll('.hero-slide'));
            const dotsWrap = document.getElementById('heroDots');
            const total = slides.length;
            if (total <= 1) return;
            let cur = 0;

            dotsWrap.innerHTML = '';
            slides.forEach((_, i) => {
                const d = document.createElement('div');
                d.className = 'promo-dot' + (i === 0 ? ' active' : '');
                d.addEventListener('click', () => heroGoTo(i));
                dotsWrap.appendChild(d);
            });

            function heroGoTo(idx) {
                cur = ((idx % total) + total) % total;
                slides.forEach((s, i) => s.classList.toggle('active', i === cur));
                dotsWrap.querySelectorAll('.promo-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
            }
            setInterval(() => heroGoTo(cur + 1), 5500);
        })();

        /* ── PROMO SLIDER (sekarang di section Promo, bukan hero) ── */
        (function() {
            const track = document.getElementById('slidesTrack');
            const dotsWrap = document.getElementById('promoDots');
            const counterEl = document.getElementById('promoCounter');
            const btnPrev = document.getElementById('promoPrev');
            const btnNext = document.getElementById('promoNext');
            const sliderEl = document.getElementById('promoSlider');
            const slides = Array.from(track.querySelectorAll('.promo-slide'));
            const total = slides.length;
            let cur = 0,
                timer = null,
                paused = false,
                touchX = 0;
            dotsWrap.innerHTML = '';
            slides.forEach((_, i) => {
                const d = document.createElement('div');
                d.className = 'promo-dot' + (i === 0 ? ' active' : '');
                d.addEventListener('click', () => promoGoTo(i));
                dotsWrap.appendChild(d);
            });

            function updateUI() {
                track.style.transform = `translateX(-${cur * 100}%)`;
                counterEl.textContent = `${cur + 1} / ${total}`;
                slides.forEach((s, i) => s.classList.toggle('active', i === cur));
                dotsWrap.querySelectorAll('.promo-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
            }
            window.promoGoTo = function(idx) {
                cur = ((idx % total) + total) % total;
                updateUI();
                clearInterval(timer);
                if (!paused) timer = setInterval(() => promoGoTo(cur + 1), 4500);
            };
            btnPrev.addEventListener('click', () => promoGoTo(cur - 1));
            btnNext.addEventListener('click', () => promoGoTo(cur + 1));
            sliderEl.addEventListener('touchstart', e => {
                touchX = e.touches[0].clientX;
                paused = true;
                clearInterval(timer);
            }, {
                passive: true
            });
            sliderEl.addEventListener('touchend', e => {
                const dx = e.changedTouches[0].clientX - touchX;
                if (Math.abs(dx) > 40) promoGoTo(cur + (dx < 0 ? 1 : -1));
                paused = false;
                promoGoTo(cur);
            });
            sliderEl.addEventListener('mouseenter', () => {
                paused = true;
                clearInterval(timer);
            });
            sliderEl.addEventListener('mouseleave', () => {
                paused = false;
                promoGoTo(cur);
            });
            updateUI();
            timer = setInterval(() => promoGoTo(cur + 1), 4500);
        })();

        /* ════════════════════════════════════════════════════════════
           SLOT PICKER DI FORM BOOKING
        ════════════════════════════════════════════════════════════ */
        const JAM_START = 9;
        const JAM_END = 20; // jam operasional

        let bookedRangesForm = [];
        let selectedSlot = null;

        function buildAllSlotsForm() {
            const slots = [];
            for (let h = JAM_START; h < JAM_END; h++) slots.push(String(h).padStart(2, '0') + ':00');
            return slots;
        }

        function isSlotBlockedForm(slot) {
            const [sh, sm] = slot.split(':').map(Number);
            const slotMin = sh * 60 + sm;

            // Ambil durasi layanan yang dipilih
            const serviceEl = document.getElementById('serviceSelect');
            const duration = serviceEl ?
                parseInt(serviceEl.options[serviceEl.selectedIndex]?.dataset?.duration || '60') :
                60;
            const slotEndMin = slotMin + duration;

            return bookedRangesForm.some(r => {
                const [rsh, rsm] = r.start.split(':').map(Number);
                const [reh, rem] = r.end.split(':').map(Number);
                const rStart = rsh * 60 + rsm;
                const rEnd = reh * 60 + rem;
                // Overlap check
                return slotMin < rEnd && slotEndMin > rStart;
            });
        }

        function renderSlotGrid() {
            const area = document.getElementById('slotArea');
            const therapistId = document.getElementById('bookingTherapistSelect').value;
            const date = document.getElementById('bookingDate').value;

            if (!therapistId || !date) {
                area.innerHTML =
                    `<div class="slot-loading" style="font-size:.82rem;">Pilih terapis dan tanggal untuk melihat ketersediaan jam.</div>`;
                return;
            }

            const allSlots = buildAllSlotsForm();
            const now = new Date();

            const html = `<div class="slot-grid">` + allSlots.map(slot => {
                const blocked = isSlotBlockedForm(slot);
                const slotDate = new Date(`${date}T${slot}`);
                const isPast = slotDate <= now;
                const disabled = blocked || isPast;
                const isSelected = slot === selectedSlot;

                if (disabled) {
                    return `<button type="button" class="slot-btn" disabled>
                <span class="slot-lock">${blocked ? '🔒' : '⏰'}</span>${slot}
            </button>`;
                }
                return `<button type="button" class="slot-btn ${isSelected ? 'selected' : ''}" onclick="pickSlot('${slot}')">
            ${slot}
        </button>`;
            }).join('') + `</div>`;

            area.innerHTML = html;
        }

        function pickSlot(slot) {
            selectedSlot = slot;
            const date = document.getElementById('bookingDate').value;
            document.getElementById('scheduledAt').value = `${date}T${slot}`;

            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = `Kirim Reservasi Jam ${slot} →`;
            }

            renderSlotGrid(); // re-render untuk highlight
        }

        async function fetchAndRenderSlots() {
            const therapistId = document.getElementById('bookingTherapistSelect')?.value;
            const date = document.getElementById('bookingDate')?.value;

            // Reset
            selectedSlot = null;
            document.getElementById('scheduledAt').value = '';
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Pilih jam terlebih dahulu';
            }

            if (!therapistId || !date) {
                renderSlotGrid();
                return;
            }

            document.getElementById('slotArea').innerHTML =
                `<div class="slot-loading">Memuat ketersediaan jam...</div>`;

            try {
                const res = await fetch(`/api/booked-slots?therapist_id=${therapistId}&date=${date}`);
                const data = await res.json();
                bookedRangesForm = data.booked || [];
            } catch (e) {
                bookedRangesForm = [];
            }

            renderSlotGrid();
        }

        // Trigger saat terapis atau tanggal berubah
        document.getElementById('bookingTherapistSelect')?.addEventListener('change', fetchAndRenderSlots);
        document.getElementById('bookingDate')?.addEventListener('change', fetchAndRenderSlots);
        document.getElementById('serviceSelect')?.addEventListener('change', renderSlotGrid); // durasi bisa berubah

        // Render awal jika ada old value (setelah validasi gagal)
        (function initSlots() {
            const tid = document.getElementById('bookingTherapistSelect')?.value;
            const date = document.getElementById('bookingDate')?.value;
            if (tid && date) fetchAndRenderSlots();
            else renderSlotGrid();
        })();

        /* ── BOOKING SUCCESS SCROLL ── */
        @if (session('booking_success'))
            window.addEventListener('load', () => {
                const el = document.getElementById('booking');
                if (el) setTimeout(() => window.scrollTo({
                    top: el.offsetTop - 80,
                    behavior: 'smooth'
                }), 300);
            });
        @endif
    </script>

</body>

</html>
