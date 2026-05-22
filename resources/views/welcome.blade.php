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
            background: rgba(250, 247, 242, .96);
            backdrop-filter: blur(14px);
            box-shadow: 0 2px 20px rgba(107, 66, 38, .08);
        }

        .navbar-logo {
            font-family: 'Playfair Display', serif;
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
            box-shadow: 0 8px 32px rgba(107, 66, 38, .12);
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

        /* ── HERO ── */
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

        .hero-inner {
            position: relative;
            width: 100%;
            max-width: 1160px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }

        @media(max-width:960px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 48px;
            }

            .hero-slider-col {
                order: -1;
            }
        }

        @media(max-width:640px) {
            #hero {
                padding: 100px 20px 60px;
            }

            .hero-slider-col {
                display: none;
            }
        }

        .hero-content {
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
                transform: scale(1)
            }

            50% {
                opacity: .4;
                transform: scale(.75)
            }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4vw, 4rem);
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

        /* ── PROMO SLIDER ── */
        .hero-slider-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeUp .9s .15s ease both;
        }

        .promo-slider-outer {
            position: relative;
            width: 100%;
            max-width: 320px;
        }

        .promo-slider-outer::before {
            content: '';
            position: absolute;
            top: 20px;
            left: -20px;
            right: 20px;
            bottom: -20px;
            background: var(--sand);
            border-radius: 28px;
            z-index: 0;
            opacity: .55;
        }

        .promo-slider-outer::after {
            content: '';
            position: absolute;
            top: 10px;
            left: -10px;
            right: 10px;
            bottom: -10px;
            background: rgba(196, 113, 75, .12);
            border-radius: 26px;
            z-index: 0;
        }

        .promo-slider-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            aspect-ratio: 3/4;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(107, 66, 38, .22), 0 8px 24px rgba(107, 66, 38, .12);
            background: #2c1f13;
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
            background: linear-gradient(180deg, rgba(44, 31, 19, 0) 25%, rgba(44, 31, 19, .25) 55%, rgba(44, 31, 19, .82) 100%);
        }

        .promo-slide-body {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 28px 24px;
            transform: translateY(8px);
            opacity: 0;
            transition: transform .5s .1s ease, opacity .5s .1s ease;
        }

        .promo-slide.active .promo-slide-body {
            transform: translateY(0);
            opacity: 1;
        }

        .promo-slide-badge {
            display: inline-block;
            padding: 4px 12px;
            background: var(--terracotta);
            border-radius: 100px;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 8px;
        }

        .promo-slide-desc {
            font-size: .78rem;
            color: rgba(255, 255, 255, .8);
            line-height: 1.6;
            margin-bottom: 12px;
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
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .promo-nav-btn:hover {
            background: rgba(255, 255, 255, .35);
            transform: scale(1.1);
        }

        .promo-counter {
            font-size: .7rem;
            color: rgba(255, 255, 255, .8);
            font-weight: 600;
            letter-spacing: .05em;
            background: rgba(0, 0, 0, .25);
            backdrop-filter: blur(6px);
            padding: 3px 10px;
            border-radius: 100px;
            border: 1px solid rgba(255, 255, 255, .15);
        }

        .promo-dots {
            position: absolute;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 10;
        }

        .promo-dot {
            width: 4px;
            height: 4px;
            border-radius: 100px;
            background: rgba(255, 255, 255, .35);
            cursor: pointer;
            transition: all .35s;
        }

        .promo-dot.active {
            background: #fff;
            height: 20px;
        }

        .promo-thumbs {
            display: flex;
            gap: 8px;
            margin-top: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .promo-thumb {
            width: 52px;
            height: 68px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2.5px solid transparent;
            transition: border-color .25s, transform .25s, opacity .25s;
            opacity: .55;
            flex-shrink: 0;
            background: var(--sand);
        }

        .promo-thumb:hover {
            opacity: .8;
        }

        .promo-thumb.active {
            border-color: var(--terracotta);
            transform: scale(1.1);
            opacity: 1;
        }

        .promo-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .promo-label {
            margin-top: 14px;
            font-size: .72rem;
            color: var(--muted);
            text-align: center;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .promo-label::before,
        .promo-label::after {
            content: '';
            display: inline-block;
            width: 24px;
            height: 1px;
            background: var(--sand);
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
            font-family: 'DM Sans', sans-serif;
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
            font-size: 2rem;
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

        /* ── TERAPIS ── */
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

        /* ── JADWAL TERAPIS ── */
        #jadwal {
            background: var(--white);
        }

        .ts-filter-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 32px;
            margin-top: 40px;
        }

        .ts-btn {
            padding: 9px 20px;
            border-radius: 100px;
            border: 1.5px solid var(--sand);
            background: transparent;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .ts-btn:hover {
            border-color: var(--terracotta);
            color: var(--brown);
        }

        .ts-btn.active {
            background: var(--terracotta);
            border-color: var(--terracotta);
            color: white;
        }

        .ts-btn-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sand), var(--terracotta));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .ts-btn.active .ts-btn-avatar {
            background: rgba(255, 255, 255, .28);
        }

        .ts-cal-wrap {
            background: var(--cream);
            border: 1.5px solid var(--sand);
            border-radius: 20px;
            padding: 32px;
        }

        .ts-cal-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .ts-nav-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid var(--sand);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--brown);
            font-size: 1rem;
            transition: all .2s;
            flex-shrink: 0;
        }

        .ts-nav-btn:hover {
            background: var(--terracotta);
            border-color: var(--terracotta);
            color: white;
        }

        .ts-month-label {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--brown);
            flex: 1;
        }

        .ts-therapist-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .ts-therapist-info-name {
            font-size: .82rem;
            font-weight: 600;
            color: var(--brown);
        }

        .ts-therapist-info-spec {
            font-size: .72rem;
            color: var(--muted);
        }

        .ts-therapist-mini-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sand), var(--terracotta));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: .9rem;
            font-weight: 700;
            color: white;
        }

        .ts-day-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .ts-day-name {
            text-align: center;
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
            padding: 6px 0;
            letter-spacing: .05em;
        }

        .ts-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .ts-cell {
            min-height: 84px;
            border-radius: 10px;
            padding: 8px 7px;
            border: 1.5px solid transparent;
            transition: transform .15s, box-shadow .15s;
            cursor: default;
        }

        .ts-cell.working {
            background: #f0faf5;
            border-color: #9fe1cb;
        }

        .ts-cell.working.has-booking {
            background: #fff8ed;
            border-color: #f5c87a;
            cursor: pointer;
        }

        .ts-cell.working.has-booking:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(196, 113, 75, .15);
        }

        .ts-cell.off {
            background: #f8f7f4;
            border-color: #e0ded9;
        }

        .ts-cell.today {
            outline: 2px solid var(--terracotta);
            outline-offset: 2px;
        }

        .ts-cell-date {
            font-size: .82rem;
            font-weight: 600;
            color: var(--brown);
            margin-bottom: 4px;
            display: block;
        }

        .ts-cell-date.off-num {
            color: var(--muted);
            font-weight: 400;
        }

        .ts-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 100px;
            font-size: .62rem;
            font-weight: 600;
        }

        .ts-badge.work {
            background: #c0dd97;
            color: #3b6d11;
        }

        .ts-badge.off {
            background: #d9d7d0;
            color: #6b6860;
        }

        .ts-badge.booked {
            background: #fde68a;
            color: #92400e;
        }

        .ts-slot-preview {
            margin-top: 3px;
        }

        .ts-slot-dot-row {
            display: flex;
            gap: 3px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 3px;
        }

        .ts-slot-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #f59e0b;
            flex-shrink: 0;
        }

        .ts-slot-dot.free {
            background: #22c55e;
        }

        .ts-slot-more-text {
            font-size: .58rem;
            color: var(--muted);
        }

        .ts-time {
            font-size: .62rem;
            color: #1d9e75;
            margin-top: 2px;
            font-weight: 500;
        }

        /* ── MODAL DETAIL BOOKING HARI ── */
        .ts-day-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 500;
            background: rgba(44, 31, 19, .55);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ts-day-modal-overlay.open {
            display: flex;
        }

        .ts-day-modal {
            background: var(--white);
            border-radius: 24px;
            padding: 32px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 32px 80px rgba(44, 31, 19, .25);
            animation: modalIn .25s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.97)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .ts-day-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 12px;
        }

        .ts-day-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--brown);
        }

        .ts-day-modal-sub {
            font-size: .8rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .ts-day-modal-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid var(--sand);
            background: var(--cream);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--muted);
            flex-shrink: 0;
            transition: all .2s;
        }

        .ts-day-modal-close:hover {
            background: var(--terracotta);
            color: white;
            border-color: var(--terracotta);
        }

        .ts-slot-timeline {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .ts-slot-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1.5px solid var(--sand);
        }

        .ts-slot-row.booked {
            background: #fff8ed;
            border-color: #f5c87a;
        }

        .ts-slot-row.free {
            background: #f0faf5;
            border-color: #9fe1cb;
        }

        .ts-slot-row-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            flex-shrink: 0;
        }

        .ts-slot-row.booked .ts-slot-row-icon {
            background: #fde68a;
        }

        .ts-slot-row.free .ts-slot-row-icon {
            background: #c0dd97;
        }

        .ts-slot-row-time {
            font-size: .85rem;
            font-weight: 600;
            color: var(--brown);
            min-width: 42px;
        }

        .ts-slot-row-label {
            font-size: .78rem;
            color: var(--muted);
            flex: 1;
        }

        .ts-slot-row-status {
            font-size: .68rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 100px;
        }

        .ts-slot-row.booked .ts-slot-row-status {
            background: #fde68a;
            color: #92400e;
        }

        .ts-slot-row.free .ts-slot-row-status {
            background: #c0dd97;
            color: #3b6d11;
        }

        .ts-modal-book-btn {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background .2s;
        }

        .ts-modal-book-btn:hover {
            background: var(--terra-dark);
        }

        .ts-summary {
            display: flex;
            gap: 14px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .ts-sum-card {
            flex: 1;
            min-width: 100px;
            background: var(--white);
            border: 1.5px solid var(--sand);
            border-radius: 14px;
            padding: 18px 16px;
            text-align: center;
        }

        .ts-sum-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--brown);
            line-height: 1;
        }

        .ts-sum-num.green {
            color: #1d9e75;
        }

        .ts-sum-num.muted {
            color: var(--muted);
        }

        .ts-sum-label {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 5px;
        }

        .ts-legend {
            display: flex;
            gap: 16px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .ts-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .75rem;
            color: var(--muted);
        }

        .ts-legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .ts-empty {
            padding: 56px 24px;
            text-align: center;
            color: var(--muted);
            background: var(--cream);
            border: 1.5px dashed var(--sand);
            border-radius: 20px;
        }

        .ts-empty-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            opacity: .5;
        }

        .ts-empty p {
            font-size: .9rem;
            line-height: 1.7;
        }

        .ts-note {
            margin-top: 20px;
            padding: 14px 18px;
            background: rgba(196, 113, 75, .07);
            border-left: 3px solid var(--terracotta);
            border-radius: 0 10px 10px 0;
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.8;
        }

        .ts-note a {
            color: var(--terracotta);
            font-weight: 600;
        }

        @media(max-width:600px) {
            .ts-cal-wrap {
                padding: 20px 14px;
            }

            .ts-cell {
                min-height: 56px;
                padding: 5px 4px;
            }

            .ts-time,
            .ts-slot-preview {
                display: none;
            }

            .ts-therapist-info {
                display: none;
            }
        }

        /* ── TENTANG ── */
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
            font-family: 'DM Sans', sans-serif;
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
            background: rgba(196, 113, 75, .06);
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

        /* ── FOOTER ── */
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

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
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
            <li><a href="#terapis">Terapis</a></li>
            <li><a href="#jadwal">Jadwal</a></li>
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

    {{-- ── MOBILE MENU ── --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="#layanan" onclick="closeMenu()">Layanan</a>
        <a href="#terapis" onclick="closeMenu()">Terapis</a>
        <a href="#jadwal" onclick="closeMenu()">Jadwal</a>
        <a href="#tentang" onclick="closeMenu()">Tentang</a>
        <a href="#booking" onclick="closeMenu()">Booking</a>
        @auth
            <a href="{{ route('dashboard') }}" class="mobile-cta" onclick="closeMenu()">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="mobile-cta">Masuk / Daftar</a>
        @endauth
    </div>

    {{-- ── HERO ── --}}
    <section id="hero">
        <div class="hero-bg"></div>
        <div class="hero-inner">
            <div class="hero-content">
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
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
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

            <div class="hero-slider-col">
                <div class="promo-slider-outer">
                    <div class="promo-slider-wrap" id="promoSlider">
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
                                $placeholderColors = ['#c4714b', '#a35a38', '#8c5c38', '#b07850', '#d4956e'];
                            @endphp
                            @foreach ($slides as $i => $slide)
                                @php
                                    $exists = file_exists(public_path('images/promos/' . $slide['file']));
                                    $color = $placeholderColors[$i % count($placeholderColors)];
                                @endphp
                                <div class="promo-slide {{ $i === 0 ? 'active' : '' }}">
                                    @if ($exists)
                                        <img class="promo-slide-img"
                                            src="{{ asset('images/promos/' . $slide['file']) }}"
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
                <div class="promo-thumbs" id="promoThumbs">
                    @foreach ($slides as $i => $slide)
                        @php $exists = file_exists(public_path('images/promos/'.$slide['file'])); @endphp
                        <div class="promo-thumb {{ $i === 0 ? 'active' : '' }}"
                            onclick="promoGoTo({{ $i }})"
                            style="{{ !$exists ? 'background:' . $placeholderColors[$i % count($placeholderColors)] . ';display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:16px;' : '' }}">
                            @if ($exists)
                                <img src="{{ asset('images/promos/' . $slide['file']) }}" loading="lazy">
                            @else
                                ✦
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="promo-label">Foto Promo Kami</div>
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

    {{-- ── TERAPIS ── --}}
    <section id="terapis">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">{{ $content['terapis_eyebrow'] ?? 'Tim Kami' }}</div>
                <h2 class="section-title">
                    {{ $content['terapis_title_1'] ?? 'Terapis Profesional' }}<br>
                    {{ $content['terapis_title_2'] ?? '& Bersertifikat' }}
                </h2>
                <p class="section-sub">
                    {{ $content['terapis_sub'] ?? 'Setiap terapis kami telah melewati pelatihan intensif dan memiliki sertifikasi resmi.' }}
                </p>
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

    {{-- ── JADWAL TERAPIS ── --}}
    <section id="jadwal">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Cek Ketersediaan</div>
                <h2 class="section-title">Jadwal & Ketersediaan<br>Terapis</h2>
                <p class="section-sub">Klik pada tanggal yang bertanda kuning untuk melihat jam mana saja yang sudah
                    dipesan dan yang masih tersedia.</p>
            </div>

            <div class="ts-filter-row fade-up" id="tsFilter">
                @forelse ($therapists ?? [] as $t)
                    <button type="button" class="ts-btn" data-id="{{ $t->id }}"
                        onclick="tsSelect({{ $t->id }}, '{{ addslashes($t->name) }}', '{{ addslashes($t->specialization ?? 'Terapis Profesional') }}')">
                        <div class="ts-btn-avatar">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                        {{ $t->name }}
                    </button>
                @empty
                    <p style="font-size:.85rem;color:var(--muted);font-style:italic;">Belum ada terapis terdaftar.</p>
                @endforelse
            </div>

            <div id="tsCalArea" class="fade-up">
                <div class="ts-empty">
                    <div class="ts-empty-icon">🗓</div>
                    <p>Pilih salah satu terapis di atas<br>untuk melihat jadwal dan ketersediaan.</p>
                </div>
            </div>

            <p class="ts-note fade-up">
                💡 Tanggal dengan tanda kuning berarti sudah ada booking di hari itu — klik untuk melihat detail jam.
                Jadwal dapat berubah sewaktu-waktu.
                <a href="#booking">Buat reservasi</a> dan tim kami konfirmasi via WhatsApp dalam 30 menit.
            </p>
        </div>
    </section>

    {{-- ── MODAL DETAIL SLOT HARIAN ── --}}
    <div class="ts-day-modal-overlay" id="tsDayModalOverlay" onclick="closeDayModal(event)">
        <div class="ts-day-modal" id="tsDayModal">
            <div class="ts-day-modal-header">
                <div>
                    <div class="ts-day-modal-title" id="tsDayModalTitle">—</div>
                    <div class="ts-day-modal-sub" id="tsDayModalSub">—</div>
                </div>
                <button class="ts-day-modal-close" onclick="closeDayModalDirect()">✕</button>
            </div>
            <div class="ts-slot-timeline" id="tsDayModalSlots">
                <div class="slot-loading">Memuat data slot...</div>
            </div>
            <button class="ts-modal-book-btn" id="tsDayModalBookBtn" onclick="bookFromModal()">
                Booking di Tanggal Ini →
            </button>
        </div>
    </div>

    {{-- ── TENTANG ── --}}
    <section id="tentang">
        <div class="section-inner">
            <div class="fade-up" style="text-align:center;max-width:600px;margin:0 auto;">
                <div class="section-eyebrow" style="justify-content:center;">
                    {{ $content['tentang_eyebrow'] ?? 'Kenapa Kami' }}</div>
                <h2 class="section-title">
                    {{ $content['tentang_title_1'] ?? 'Pengalaman Spa yang' }}<br>
                    {{ $content['tentang_title_2'] ?? 'Berbeda dari yang Lain' }}
                </h2>
                <p class="section-sub" style="margin:0 auto;">
                    {{ $content['tentang_sub'] ?? 'Kami berkomitmen memberikan pengalaman wellness terbaik dengan standar pelayanan tertinggi.' }}
                </p>
            </div>
            <div class="why-grid">
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
                            'text' => 'Ruangan terapi privat yang tenang dan nyaman untuk pengalaman terbaik Anda.',
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
                @foreach ($whyCards as $card)
                    <div class="why-card fade-up">
                        <div class="why-icon">{{ $content[$card['key'] . '_icon'] ?? $card['icon'] }}</div>
                        <div class="why-title">{{ $content[$card['key'] . '_title'] ?? $card['title'] }}</div>
                        <div class="why-text">{{ $content[$card['key'] . '_text'] ?? $card['text'] }}</div>
                    </div>
                @endforeach
            </div>
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
                                style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--brown);margin-bottom:12px;">
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
                        <li><a href="#terapis">Terapis</a></li>
                        <li><a href="#jadwal">Jadwal</a></li>
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
                        <li><a href="#">📍
                                {{ $content['footer_address'] ?? 'Jl. Melati Raya No. 47, Cirebon' }}</a></li>
                        <li><a href="#">📞 {{ $content['footer_phone'] ?? '0821-5567-3894' }}</a></li>
                        <li><a href="#">✉ {{ $content['footer_email'] ?? 'hello@koichispa.id' }}</a></li>
                        <li><a href="#">⏰ {{ $content['footer_hours'] ?? '09.00 – 20.00' }}</a></li>
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

        /* ── PROMO SLIDER ── */
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
                document.querySelectorAll('.promo-thumb').forEach((t, i) => t.classList.toggle('active', i === cur));
                const th = document.querySelectorAll('.promo-thumb')[cur];
                if (th) {
                    const container = th.parentElement;
                    const offset = th.offsetLeft - container.offsetLeft;
                    container.scrollTo({
                        left: offset - (container.offsetWidth / 2) + (th.offsetWidth / 2),
                        behavior: 'smooth'
                    });
                }
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
            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') promoGoTo(cur - 1);
                if (e.key === 'ArrowRight') promoGoTo(cur + 1);
            });
            updateUI();
            timer = setInterval(() => promoGoTo(cur + 1), 4500);
        })();

        /* ════════════════════════════════════════════════════════════
           JADWAL TERAPIS
        ════════════════════════════════════════════════════════════ */
        const TS_DATA = @json($therapistSchedules ?? []);
        const DAY_NAMES = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const MONTH_NAMES = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];

        let tsActiveId = null;
        let tsCurYear = new Date().getFullYear();
        let tsCurMonth = new Date().getMonth();

        // State modal
        let modalActiveDate = null;
        let modalActiveTherapistId = null;

        function tsSelect(id, name, spec) {
            tsActiveId = id;
            document.querySelectorAll('.ts-btn').forEach(b => b.classList.toggle('active', parseInt(b.dataset.id) === id));
            tsRenderCal();
        }

        function tsRenderCal() {
            const area = document.getElementById('tsCalArea');
            if (!tsActiveId || !TS_DATA[tsActiveId]) {
                area.innerHTML =
                    `<div class="ts-empty"><div class="ts-empty-icon">🗓</div><p>Data jadwal belum tersedia untuk terapis ini.</p></div>`;
                return;
            }

            const t = TS_DATA[tsActiveId];
            const scheds = t.schedules || {};
            const bookingMap = t.booking_map || {};
            const today = new Date();
            const lastDate = new Date(tsCurYear, tsCurMonth + 1, 0).getDate();
            const startDow = new Date(tsCurYear, tsCurMonth, 1).getDay();

            // Bangun semua jam operasional
            const startH = parseInt((t.start_time || '09:00').split(':')[0]);
            const endH = parseInt((t.end_time || '20:00').split(':')[0]);
            const totalSlots = endH - startH;

            let workCount = 0,
                offCount = 0,
                cells = '';
            for (let i = 0; i < startDow; i++) cells += `<div></div>`;

            for (let d = 1; d <= lastDate; d++) {
                const ds = `${tsCurYear}-${String(tsCurMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                const status = scheds[ds] || 'nodata';
                const dayBooks = bookingMap[ds] || [];
                const cellDate = new Date(tsCurYear, tsCurMonth, d);
                const isToday = cellDate.toDateString() === today.toDateString();

                if (status === 'working') workCount++;
                else if (status !== 'nodata') offCount++;

                let cellCls = 'ts-cell';
                let dateCls = 'ts-cell-date';
                let badge = '';
                let preview = '';

                if (status === 'working') {
                    const hasBook = dayBooks.length > 0;
                    cellCls += ' working' + (hasBook ? ' has-booking' : '');
                    badge = hasBook ?
                        `<span class="ts-badge booked">📋 ${dayBooks.length} booking</span>` :
                        `<span class="ts-badge work">Tersedia</span>`;

                    // Titik-titik slot (maks 6 tampil)
                    if (hasBook) {
                        const slotsLeft = totalSlots - dayBooks.length;
                        const dots = dayBooks.slice(0, 6).map(() => `<div class="ts-slot-dot"></div>`).join('');
                        const freeDots = Math.min(slotsLeft, 3) > 0 ?
                            Array(Math.min(slotsLeft, 3)).fill(`<div class="ts-slot-dot free"></div>`).join('') :
                            '';
                        preview =
                            `<div class="ts-slot-dot-row">${dots}${freeDots}${dayBooks.length > 6 ? `<span class="ts-slot-more-text">+${dayBooks.length-6}</span>` : ''}</div>`;
                    } else {
                        preview = `<div class="ts-time">${t.start_time||'09:00'} – ${t.end_time||'20:00'}</div>`;
                    }
                } else if (['off', 'sick', 'vacation', 'cuti_bersama'].includes(status)) {
                    cellCls += ' off';
                    dateCls += ' off-num';
                    const labels = {
                        off: 'Libur',
                        sick: 'Sakit',
                        vacation: 'Liburan',
                        cuti_bersama: 'Cuti'
                    };
                    badge = `<span class="ts-badge off">${labels[status]}</span>`;
                } else {
                    cellCls += ' off';
                    dateCls += ' off-num';
                    badge = `<span class="ts-badge off" style="opacity:.35">—</span>`;
                }

                if (isToday) cellCls += ' today';

                const hasBooking = (scheds[ds] === 'working') && (bookingMap[ds] || []).length > 0;
                const clickAttr = hasBooking ? `onclick="openDayModal('${ds}', ${tsActiveId})"` : '';

                cells += `<div class="${cellCls}" ${clickAttr}>
            <span class="${dateCls}">${d}</span>
            ${badge}
            ${preview}
        </div>`;
            }

            area.innerHTML = `
        <div class="ts-cal-wrap">
            <div class="ts-cal-nav">
                <button class="ts-nav-btn" onclick="tsPrev()">&#8249;</button>
                <button class="ts-nav-btn" onclick="tsNext()">&#8250;</button>
                <span class="ts-month-label">${MONTH_NAMES[tsCurMonth]} ${tsCurYear}</span>
                <div class="ts-therapist-info">
                    <div>
                        <div class="ts-therapist-info-name">${t.name}</div>
                        <div class="ts-therapist-info-spec">${t.spec}</div>
                    </div>
                    <div class="ts-therapist-mini-avatar">${t.name.charAt(0).toUpperCase()}</div>
                </div>
            </div>
            <div class="ts-day-header">${DAY_NAMES.map(n=>`<div class="ts-day-name">${n}</div>`).join('')}</div>
            <div class="ts-grid">${cells}</div>
            <div class="ts-summary">
                <div class="ts-sum-card"><div class="ts-sum-num green">${workCount}</div><div class="ts-sum-label">Hari Masuk</div></div>
                <div class="ts-sum-card"><div class="ts-sum-num muted">${offCount}</div><div class="ts-sum-label">Hari Libur</div></div>
                <div class="ts-sum-card">
                    <div class="ts-sum-num" style="color:var(--terracotta);font-size:1.1rem;padding-top:4px;">${t.start_time||'09:00'} – ${t.end_time||'20:00'}</div>
                    <div class="ts-sum-label">Jam Operasional</div>
                </div>
            </div>
            <div class="ts-legend">
                <div class="ts-legend-item"><div class="ts-legend-dot" style="background:#c0dd97;border:1px solid #9fe1cb;"></div> Tersedia</div>
                <div class="ts-legend-item"><div class="ts-legend-dot" style="background:#fff8ed;border:1px solid #f5c87a;"></div> Ada booking (klik untuk detail)</div>
                <div class="ts-legend-item"><div class="ts-legend-dot" style="background:#d9d7d0;border:1px solid #c4c2bb;"></div> Libur</div>
                <div class="ts-legend-item"><div class="ts-legend-dot" style="background:transparent;border:2px solid var(--terracotta);border-radius:50%;"></div> Hari ini</div>
            </div>
        </div>`;
        }

        function tsPrev() {
            tsCurMonth--;
            if (tsCurMonth < 0) {
                tsCurMonth = 11;
                tsCurYear--;
            }
            tsRenderCal();
        }

        function tsNext() {
            tsCurMonth++;
            if (tsCurMonth > 11) {
                tsCurMonth = 0;
                tsCurYear++;
            }
            tsRenderCal();
        }

        /* ── MODAL DETAIL SLOT ── */
        async function openDayModal(dateStr, therapistId) {
            modalActiveDate = dateStr;
            modalActiveTherapistId = therapistId;

            const t = TS_DATA[therapistId];

            // Format tanggal Indonesia
            const [y, m, d] = dateStr.split('-');
            const MONTHS_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            const DAYS_ID = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const dayName = DAYS_ID[new Date(dateStr).getDay()];
            const formatted = `${dayName}, ${parseInt(d)} ${MONTHS_ID[parseInt(m)-1]} ${y}`;

            document.getElementById('tsDayModalTitle').textContent = formatted;
            document.getElementById('tsDayModalSub').textContent = `Jadwal ${t.name} · ${t.spec}`;
            document.getElementById('tsDayModalSlots').innerHTML =
                '<div class="slot-loading">Memuat data slot...</div>';
            document.getElementById('tsDayModalOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';

            // Bangun semua slot jam operasional
            const startH = parseInt((t.start_time || '09:00').split(':')[0]);
            const endH = parseInt((t.end_time || '20:00').split(':')[0]);
            const allSlots = [];
            for (let h = startH; h < endH; h++) allSlots.push(String(h).padStart(2, '0') + ':00');

            // Ambil data booking via AJAX
            let bookedRanges = [];
            try {
                const res = await fetch(`/api/bookings-by-date?therapist_id=${therapistId}&date=${dateStr}`);
                const data = await res.json();
                bookedRanges = data.bookings || [];
            } catch (e) {
                bookedRanges = [];
            }

            // Tentukan slot mana yang terisi berdasarkan range
            function isSlotBooked(slot) {
                const [sh, sm] = slot.split(':').map(Number);
                const slotMin = sh * 60 + sm;
                return bookedRanges.find(r => {
                    const [rsh, rsm] = r.time.split(':').map(Number);
                    const [reh, rem] = r.end_time.split(':').map(Number);
                    return slotMin >= rsh * 60 + rsm && slotMin < reh * 60 + rem;
                });
            }

            const slotsHtml = allSlots.map(slot => {
                const booking = isSlotBooked(slot);
                if (booking) {
                    return `<div class="ts-slot-row booked">
                <div class="ts-slot-row-icon">🔒</div>
                <div class="ts-slot-row-time">${slot}</div>
                <div class="ts-slot-row-label">${booking.service} (${booking.duration} mnt)</div>
                <div class="ts-slot-row-status">Dipesan</div>
            </div>`;
                } else {
                    return `<div class="ts-slot-row free">
                <div class="ts-slot-row-icon">✓</div>
                <div class="ts-slot-row-time">${slot}</div>
                <div class="ts-slot-row-label">Slot tersedia</div>
                <div class="ts-slot-row-status">Bebas</div>
            </div>`;
                }
            }).join('');

            document.getElementById('tsDayModalSlots').innerHTML = slotsHtml;
        }

        function closeDayModal(event) {
            if (event.target === document.getElementById('tsDayModalOverlay')) closeDayModalDirect();
        }

        function closeDayModalDirect() {
            document.getElementById('tsDayModalOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        function bookFromModal() {
            closeDayModalDirect();
            // Isi tanggal di form booking otomatis
            if (modalActiveDate) {
                const dateInput = document.getElementById('bookingDate');
                if (dateInput) dateInput.value = modalActiveDate;
            }
            if (modalActiveTherapistId) {
                const sel = document.getElementById('bookingTherapistSelect');
                if (sel) sel.value = modalActiveTherapistId;
            }
            // Fetch slot & render
            fetchAndRenderSlots();
            // Scroll ke booking
            setTimeout(() => {
                document.getElementById('booking').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }

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
