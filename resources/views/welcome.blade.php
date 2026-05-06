<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koichi Pijat Refleksi — Wellness & Terapi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap"
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
            font-family: 'Jost', sans-serif;
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
            z-index: 200;
            padding: 0 48px;
            height: 72px;
            display: flex;
            align-items: center;
            gap: 0;
            transition: background .4s, box-shadow .4s;
        }

        .navbar.scrolled {
            background: rgba(250, 247, 242, .97);
            backdrop-filter: blur(16px);
            box-shadow: 0 1px 0 rgba(107, 66, 38, .12);
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            margin-right: 48px;
        }

        .navbar-logo-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--brown);
            letter-spacing: -.02em;
        }

        .navbar-logo-text em {
            font-style: italic;
            color: var(--terracotta);
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 0;
            list-style: none;
            flex: 1;
        }

        .navbar-links li a {
            display: block;
            padding: 0 16px;
            height: 72px;
            line-height: 72px;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--brown);
            opacity: .7;
            transition: opacity .2s;
            position: relative;
            white-space: nowrap;
        }

        .navbar-links li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 16px;
            right: 16px;
            height: 2px;
            background: var(--terracotta);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s;
        }

        .navbar-links li a:hover {
            opacity: 1;
        }

        .navbar-links li a:hover::after {
            transform: scaleX(1);
        }

        .navbar-auth {
            margin-left: auto;
        }

        .navbar-cta {
            padding: 9px 22px;
            background: var(--terracotta);
            color: white;
            border-radius: 100px;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            transition: background .2s;
            white-space: nowrap;
        }

        .navbar-cta:hover {
            background: var(--terra-dark);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
            background: none;
            border: none;
            margin-left: auto;
        }

        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--brown);
            border-radius: 2px;
        }

        .mobile-menu {
            display: none !important;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: var(--cream);
            border-top: 1px solid var(--sand);
            padding: 20px 24px 28px;
            flex-direction: column;
            z-index: 199;
            box-shadow: 0 8px 32px rgba(107, 66, 38, .12);
        }

        .mobile-menu.open {
            display: flex !important;
        }

        .mobile-menu a {
            color: var(--brown) !important;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: 13px 0;
            border-bottom: 1px solid var(--warm);
            display: block;
        }

        .mobile-menu .mobile-cta {
            margin-top: 12px;
            padding: 14px;
            text-align: center;
            background: var(--terracotta) !important;
            color: white !important;
            border-radius: 10px;
            border-bottom: none !important;
        }

        @media(max-width:900px) {
            .navbar {
                padding: 0 20px;
            }

            .navbar-links {
                display: none;
            }

            .navbar-auth {
                display: none;
            }

            .hamburger {
                display: flex;
            }
        }

        /* ══ HERO CAROUSEL ══ */
        #hero {
            position: relative;
            width: 100%;
            height: 100vh;
            min-height: 600px;
            overflow: hidden;
            background: #1a0f08;
        }

        .hero-track {
            display: flex;
            height: 100%;
            transition: transform .85s cubic-bezier(.77, 0, .175, 1);
            will-change: transform;
        }

        .hero-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            flex-shrink: 0;
        }

        .hero-slide-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.06);
            transition: transform 9s ease;
        }

        .hero-slide.active .hero-slide-img {
            transform: scale(1);
        }

        .hero-slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, rgba(20, 10, 4, .75) 0%, rgba(20, 10, 4, .4) 55%, rgba(20, 10, 4, .18) 100%);
        }

        .hero-content {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 0 80px 80px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 16px;
            border: 1px solid rgba(232, 213, 183, .4);
            border-radius: 100px;
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(232, 213, 183, .85);
            margin-bottom: 24px;
            width: fit-content;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .7s .1s, transform .7s .1s;
        }

        .hero-slide.active .hero-badge {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-badge::before {
            content: '';
            width: 5px;
            height: 5px;
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
                transform: scale(.7)
            }
        }

        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(3rem, 6vw, 6.5rem);
            font-weight: 300;
            line-height: 1.05;
            color: #fff;
            margin-bottom: 24px;
            max-width: 700px;
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .75s .25s, transform .75s .25s;
        }

        .hero-slide.active .hero-title {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-title em {
            font-style: italic;
            color: var(--sand);
        }

        .hero-desc {
            font-size: .92rem;
            font-weight: 300;
            line-height: 1.9;
            color: rgba(255, 255, 255, .72);
            max-width: 420px;
            margin-bottom: 36px;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .7s .4s, transform .7s .4s;
        }

        .hero-slide.active .hero-desc {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .65s .55s, transform .65s .55s;
        }

        .hero-slide.active .hero-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-primary {
            padding: 14px 34px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 100px;
            font-family: 'Jost', sans-serif;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .2s;
        }

        .btn-primary:hover {
            background: var(--terra-dark);
            transform: translateY(-2px);
        }

        .btn-ghost {
            padding: 14px 30px;
            background: transparent;
            color: rgba(255, 255, 255, .85);
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 100px;
            font-size: .75rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: border-color .2s, background .2s;
        }

        .btn-ghost:hover {
            border-color: rgba(255, 255, 255, .7);
            background: rgba(255, 255, 255, .08);
        }

        /* Dots nav */
        .hero-nav {
            position: absolute;
            right: 48px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 20;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .hero-nav-dot {
            width: 3px;
            border-radius: 100px;
            background: rgba(255, 255, 255, .3);
            cursor: pointer;
            transition: all .35s;
            height: 20px;
        }

        .hero-nav-dot.active {
            background: #fff;
            height: 40px;
        }

        /* Arrows */
        .hero-arrows {
            position: absolute;
            bottom: 80px;
            right: 80px;
            z-index: 20;
            display: flex;
            gap: 10px;
        }

        .hero-arrow {
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1rem;
            transition: background .2s, border-color .2s, transform .2s;
        }

        .hero-arrow:hover {
            background: rgba(255, 255, 255, .2);
            border-color: rgba(255, 255, 255, .6);
            transform: scale(1.05);
        }

        /* Counter */
        .hero-counter {
            position: absolute;
            bottom: 86px;
            left: 80px;
            z-index: 20;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            color: rgba(255, 255, 255, .5);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-counter strong {
            color: rgba(255, 255, 255, .9);
            font-weight: 400;
            font-size: 1.1rem;
        }

        /* Progress */
        .hero-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, .12);
            z-index: 20;
        }

        .hero-progress-bar {
            height: 100%;
            background: var(--terracotta);
            width: 0%;
            transition: width 5s linear;
        }

        @media(max-width:768px) {
            .hero-content {
                padding: 0 24px 100px;
            }

            .hero-nav {
                right: 20px;
            }

            .hero-arrows {
                right: 24px;
                bottom: 100px;
            }

            .hero-counter {
                left: 24px;
                bottom: 106px;
            }
        }

        /* ══ SECTIONS ══ */
        section {
            padding: 110px 48px;
        }

        .section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-eyebrow {
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--terracotta);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .section-eyebrow::before {
            content: '';
            width: 32px;
            height: 1px;
            background: var(--terracotta);
            opacity: .6;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.4rem, 4vw, 3.8rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--brown);
            margin-bottom: 18px;
        }

        .section-title em {
            font-style: italic;
            color: var(--terracotta);
        }

        .section-sub {
            font-size: .9rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.9;
            max-width: 500px;
        }

        @media(max-width:640px) {
            section {
                padding: 72px 20px;
            }
        }

        /* ══ ABOUT ══ */
        #about {
            background: var(--white);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-top: 60px;
        }

        @media(max-width:860px) {
            .about-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .about-img-wrap {
            position: relative;
            aspect-ratio: 4/5;
            border-radius: 4px;
            overflow: hidden;
        }

        .about-img-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, var(--sand), var(--terracotta));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            opacity: .3;
        }

        .about-stat-row {
            display: flex;
            gap: 40px;
            margin-top: 48px;
            padding-top: 40px;
            border-top: 1px solid var(--sand);
            flex-wrap: wrap;
        }

        .about-stat-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-weight: 300;
            color: var(--brown);
            line-height: 1;
        }

        .about-stat-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: 6px;
        }

        .why-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 36px;
        }

        .why-item {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }

        .why-icon {
            width: 42px;
            height: 42px;
            background: rgba(196, 113, 75, .1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .why-title {
            font-weight: 600;
            font-size: .88rem;
            color: var(--brown);
            margin-bottom: 4px;
        }

        .why-text {
            font-size: .82rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.7;
        }

        /* ══ TREATMENTS ══ */
        #treatments {
            background: var(--cream);
        }

        .treatments-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 56px;
            flex-wrap: wrap;
            gap: 24px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1px;
            background: var(--sand);
            border: 1px solid var(--sand);
        }

        .service-card {
            background: var(--white);
            padding: 44px 36px;
            position: relative;
            overflow: hidden;
            transition: background .3s;
        }

        .service-card:hover {
            background: var(--cream);
        }

        .service-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 300;
            color: rgba(196, 113, 75, .15);
            line-height: 1;
            margin-bottom: 16px;
        }

        .service-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 400;
            color: var(--brown);
            margin-bottom: 10px;
        }

        .service-desc {
            font-size: .82rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 400;
            color: var(--terracotta);
        }

        .service-duration {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 5px 12px;
            border: 1px solid var(--sand);
            border-radius: 100px;
        }

        /* ══ PROMOTIONS ══ */
        #promotions {
            background: var(--warm);
        }

        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 56px;
        }

        .promo-card {
            border-radius: 4px;
            overflow: hidden;
            background: var(--white);
            border: 1px solid var(--sand);
            transition: transform .3s, box-shadow .3s;
        }

        .promo-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 56px rgba(107, 66, 38, .12);
        }

        .promo-card-img {
            width: 100%;
            aspect-ratio: 3/2;
            object-fit: cover;
            display: block;
        }

        .promo-card-placeholder {
            width: 100%;
            aspect-ratio: 3/2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .promo-card-body {
            padding: 24px;
        }

        .promo-badge {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(196, 113, 75, .12);
            color: var(--terracotta);
            border-radius: 100px;
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .promo-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 400;
            color: var(--brown);
            margin-bottom: 8px;
        }

        .promo-desc {
            font-size: .8rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.7;
        }

        /* ══ RESERVATION ══ */
        #reservation {
            background: var(--white);
        }

        .reservation-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
        }

        @media(max-width:900px) {
            .reservation-wrapper {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .booking-features {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 40px;
        }

        .booking-feature {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }

        .booking-feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(196, 113, 75, .1);
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .booking-feature-title {
            font-weight: 600;
            font-size: .88rem;
            color: var(--brown);
            margin-bottom: 4px;
        }

        .booking-feature-desc {
            font-size: .8rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.7;
        }

        .booking-form-card {
            background: var(--cream);
            border-radius: 4px;
            padding: 48px 44px;
            border: 1px solid var(--sand);
        }

        @media(max-width:640px) {
            .booking-form-card {
                padding: 28px 20px;
            }
        }

        .form-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            color: var(--brown);
            margin-bottom: 8px;
        }

        .form-sub {
            font-size: .82rem;
            font-weight: 300;
            color: var(--muted);
            margin-bottom: 32px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: .6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 13px 18px;
            background: var(--white);
            border: 1px solid var(--sand);
            border-radius: 4px;
            font-family: 'Jost', sans-serif;
            font-size: .88rem;
            font-weight: 300;
            color: var(--text);
            outline: none;
            transition: border-color .2s;
            -webkit-appearance: none;
        }

        .form-control:focus {
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(196, 113, 75, .08);
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
            padding: 15px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: 'Jost', sans-serif;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, transform .2s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: var(--terra-dark);
            transform: translateY(-1px);
        }

        .submit-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
        }

        .slot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 4px;
        }

        .slot-btn {
            padding: 9px 4px;
            border-radius: 4px;
            font-family: 'Jost', sans-serif;
            font-size: .75rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            border: 1px solid var(--sand);
            background: var(--white);
            color: var(--brown);
            transition: all .15s;
            line-height: 1.3;
        }

        .slot-btn:hover:not(:disabled) {
            border-color: var(--terracotta);
            background: rgba(196, 113, 75, .05);
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

        .slot-loading {
            text-align: center;
            padding: 20px;
            color: var(--muted);
            font-size: .82rem;
            font-weight: 300;
        }

        .slot-hint {
            font-size: .7rem;
            font-weight: 300;
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

        /* ══ GIFT ══ */
        #gift {
            background: var(--brown);
            overflow: hidden;
            position: relative;
        }

        .gift-bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .03);
        }

        .gift-inner {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        @media(max-width:860px) {
            .gift-inner {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .gift-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--sand);
            margin-bottom: 20px;
        }

        .gift-title em {
            font-style: italic;
            color: rgba(232, 213, 183, .6);
        }

        .gift-sub {
            font-size: .88rem;
            font-weight: 300;
            color: rgba(255, 255, 255, .55);
            line-height: 1.9;
            margin-bottom: 36px;
        }

        .gift-card {
            background: var(--cream);
            border-radius: 4px;
            padding: 44px 40px;
            position: relative;
            overflow: hidden;
        }

        .gift-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--terracotta), var(--sand));
        }

        .gift-card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 400;
            color: var(--brown);
            margin-bottom: 8px;
        }

        .gift-card-sub {
            font-size: .82rem;
            font-weight: 300;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.7;
        }

        .gift-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .gift-opt {
            padding: 14px 8px;
            border: 1px solid var(--sand);
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
        }

        .gift-opt:hover,
        .gift-opt.selected {
            border-color: var(--terracotta);
            background: rgba(196, 113, 75, .06);
        }

        .gift-opt-amount {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            font-weight: 400;
            color: var(--brown);
        }

        .gift-opt-label {
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .08em;
            color: var(--muted);
            margin-top: 2px;
        }

        .btn-gift {
            display: block;
            width: 100%;
            padding: 14px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: 'Jost', sans-serif;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            text-align: center;
            transition: background .2s;
            margin-top: 4px;
        }

        .btn-gift:hover {
            background: var(--terra-dark);
        }

        /* ══ LOCATION ══ */
        #location {
            background: var(--cream);
        }

        .location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
            margin-top: 56px;
        }

        @media(max-width:860px) {
            .location-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .location-map {
            aspect-ratio: 4/3;
            background: var(--sand);
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid var(--sand);
        }

        .location-map iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .location-info {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .location-item {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }

        .location-item-icon {
            width: 44px;
            height: 44px;
            border: 1px solid var(--sand);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .location-item-label {
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .location-item-value {
            font-size: .9rem;
            font-weight: 400;
            color: var(--brown);
            line-height: 1.6;
        }

        /* ══ FOOTER ══ */
        footer {
            background: #1a0f08;
            color: rgba(255, 255, 255, .5);
            padding: 60px 48px 32px;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 48px;
            padding-bottom: 48px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .footer-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            color: var(--sand);
            margin-bottom: 12px;
        }

        .footer-brand em {
            font-style: italic;
            color: rgba(232, 213, 183, .5);
        }

        .footer-tagline {
            font-size: .8rem;
            font-weight: 300;
            max-width: 240px;
            line-height: 1.8;
        }

        .footer-links h4 {
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .6);
            margin-bottom: 18px;
        }

        .footer-links ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            font-size: .82rem;
            font-weight: 300;
            color: rgba(255, 255, 255, .4);
            transition: color .2s;
        }

        .footer-links a:hover {
            color: rgba(255, 255, 255, .85);
        }

        .footer-bottom {
            padding-top: 24px;
            font-size: .75rem;
            font-weight: 300;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ── FADE UP ── */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media(max-width:640px) {
            footer {
                padding: 48px 20px 24px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar" id="mainNav">
        <a href="#hero" class="navbar-logo">
            <span class="navbar-logo-text">Koichi <em>Spa</em></span>
        </a>
        <ul class="navbar-links">
            <li><a href="#about">About</a></li>
            <li><a href="#treatments">Treatments</a></li>
            <li><a href="#promotions">Promotions</a></li>
            <li><a href="#reservation">Reservation</a></li>
            <li><a href="#gift">Gift Certificate</a></li>
            <li><a href="#location">Location</a></li>
        </ul>
        <div class="navbar-auth">
            <a href="#" class="navbar-cta">Sign In</a>
        </div>
        <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="#about" onclick="closeMenu()">About</a>
        <a href="#treatments" onclick="closeMenu()">Treatments</a>
        <a href="#promotions" onclick="closeMenu()">Promotions</a>
        <a href="#reservation" onclick="closeMenu()">Reservation</a>
        <a href="#gift" onclick="closeMenu()">Gift Certificate</a>
        <a href="#location" onclick="closeMenu()">Location</a>
        <a href="#" class="mobile-cta">Sign In</a>
    </div>

    <!-- HERO CAROUSEL -->
    <section id="hero">
        <div class="hero-track" id="heroTrack">
            <!-- Slide 1 -->
            <div class="hero-slide active">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#3d2010,#7a3b1e,#c4714b55);">
                </div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">Buka Setiap Hari · 09.00 – 20.00</div>
                    <h1 class="hero-title">Temukan <em>Kedamaian</em><br>di Tengah Kesibukan</h1>
                    <p class="hero-desc">Layanan pijat & terapi profesional untuk memulihkan tubuh, pikiran, dan jiwa
                        Anda. Dipercaya lebih dari 500 pelanggan setia.</p>
                    <div class="hero-actions">
                        <a href="#reservation" class="btn-primary">Book Now <svg width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></a>
                        <a href="#treatments" class="btn-ghost">Explore Treatments</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="hero-slide">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a3028,#2d5040,#4a7a5588);">
                </div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">Weekend Special Deal</div>
                    <h1 class="hero-title">Rasakan <em>Ketenangan</em><br>yang Sesungguhnya</h1>
                    <p class="hero-desc">Paket eksklusif akhir pekan dengan terapis bersertifikat dan bahan alami
                        premium pilihan kami.</p>
                    <div class="hero-actions">
                        <a href="#reservation" class="btn-primary">Book Now <svg width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></a>
                        <a href="#promotions" class="btn-ghost">Lihat Promo</a>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="hero-slide">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1e1030,#3a2060,#6b4b9088);">
                </div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">Hot Deal Bulan Ini</div>
                    <h1 class="hero-title">Pulihkan <em>Keseimbangan</em><br>Tubuh & Pikiran</h1>
                    <p class="hero-desc">Teknik refleksiologi tradisional dikombinasikan dengan perawatan modern untuk
                        hasil terbaik.</p>
                    <div class="hero-actions">
                        <a href="#reservation" class="btn-primary">Book Now <svg width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></a>
                        <a href="#treatments" class="btn-ghost">Explore Treatments</a>
                    </div>
                </div>
            </div>
            <!-- Slide 4 -->
            <div class="hero-slide">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#301028,#602040,#a04060aa);">
                </div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">Facial Treatment</div>
                    <h1 class="hero-title">Cerahkan <em>Kulit Anda</em><br>dengan Perawatan Terbaik</h1>
                    <p class="hero-desc">Facial premium dengan bahan organik pilihan — kulit bercahaya, lebih muda, dan
                        sehat alami.</p>
                    <div class="hero-actions">
                        <a href="#reservation" class="btn-primary">Book Now <svg width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></a>
                        <a href="#treatments" class="btn-ghost">Explore Treatments</a>
                    </div>
                </div>
            </div>
            <!-- Slide 5 -->
            <div class="hero-slide">
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#102030,#204060,#3060a088);">
                </div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">Membership Eksklusif</div>
                    <h1 class="hero-title">Bergabung <em>Bersama</em><br>500+ Pelanggan Setia</h1>
                    <p class="hero-desc">Dapatkan keuntungan member eksklusif dengan akses prioritas booking dan diskon
                        spesial.</p>
                    <div class="hero-actions">
                        <a href="#reservation" class="btn-primary">Book Now <svg width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></a>
                        <a href="#gift" class="btn-ghost">Gift Certificate</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-nav" id="heroNav"></div>
        <div class="hero-counter" id="heroCounter"><strong>01</strong> / 05</div>
        <div class="hero-arrows">
            <button class="hero-arrow" id="heroPrev" aria-label="Previous">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <button class="hero-arrow" id="heroNext" aria-label="Next">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>
        <div class="hero-progress">
            <div class="hero-progress-bar" id="heroProgressBar"></div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about">
        <div class="section-inner">
            <div class="about-grid">
                <div class="about-img-wrap fade-up">
                    <div class="about-img-placeholder">🌿</div>
                </div>
                <div class="fade-up">
                    <div class="section-eyebrow">About Us</div>
                    <h2 class="section-title">Pengalaman Spa yang<br><em>Berbeda dari yang Lain</em></h2>
                    <p class="section-sub">Kami berkomitmen memberikan pengalaman wellness terbaik dengan standar
                        pelayanan tertinggi, menggunakan teknik tradisional dan bahan alami premium.</p>
                    <div class="why-list">
                        <div class="why-item">
                            <div class="why-icon">🏅</div>
                            <div>
                                <div class="why-title">Terapis Bersertifikat</div>
                                <div class="why-text">Semua terapis kami bersertifikat nasional & internasional dengan
                                    pengalaman minimal 3 tahun.</div>
                            </div>
                        </div>
                        <div class="why-item">
                            <div class="why-icon">🌿</div>
                            <div>
                                <div class="why-title">Bahan Alami Premium</div>
                                <div class="why-text">Kami hanya menggunakan produk organik berkualitas tinggi yang
                                    aman untuk kulit Anda.</div>
                            </div>
                        </div>
                        <div class="why-item">
                            <div class="why-icon">💎</div>
                            <div>
                                <div class="why-title">Harga Transparan</div>
                                <div class="why-text">Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga
                                    yang Anda bayar.</div>
                            </div>
                        </div>
                    </div>
                    <div class="about-stat-row">
                        <div>
                            <div class="about-stat-num">500+</div>
                            <div class="about-stat-label">Pelanggan Puas</div>
                        </div>
                        <div>
                            <div class="about-stat-num">15+</div>
                            <div class="about-stat-label">Terapis Aktif</div>
                        </div>
                        <div>
                            <div class="about-stat-num">8+</div>
                            <div class="about-stat-label">Jenis Layanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TREATMENTS -->
    <section id="treatments">
        <div class="section-inner">
            <div class="treatments-header fade-up">
                <div>
                    <div class="section-eyebrow">Our Services</div>
                    <h2 class="section-title">Pilihan Terapi<br><em>Terbaik untuk Anda</em></h2>
                </div>
                <p class="section-sub">Setiap layanan dirancang oleh terapis bersertifikat menggunakan teknik terbaik
                    dan bahan alami pilihan.</p>
            </div>
            <div class="services-grid fade-up">
                <div class="service-card">
                    <div class="service-num">01</div>
                    <div class="service-name">Swedish Massage</div>
                    <div class="service-desc">Teknik pijat klasik untuk melepaskan ketegangan otot dan meningkatkan
                        sirkulasi darah secara menyeluruh.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 150.000</div>
                        <div class="service-duration">60 mnt</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-num">02</div>
                    <div class="service-name">Aromaterapi</div>
                    <div class="service-desc">Kombinasi pijat lembut dengan minyak esensial pilihan untuk ketenangan
                        pikiran dan jiwa yang mendalam.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 180.000</div>
                        <div class="service-duration">75 mnt</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-num">03</div>
                    <div class="service-name">Hot Stone Therapy</div>
                    <div class="service-desc">Batu vulkanik panas di titik-titik energi tubuh untuk relaksasi mendalam
                        dan pemulihan optimal.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 220.000</div>
                        <div class="service-duration">90 mnt</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-num">04</div>
                    <div class="service-name">Facial Spa</div>
                    <div class="service-desc">Perawatan wajah mendalam dengan teknologi modern dan bahan organik
                        berkualitas tinggi.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 200.000</div>
                        <div class="service-duration">60 mnt</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-num">05</div>
                    <div class="service-name">Refleksiologi</div>
                    <div class="service-desc">Pijat kaki berbasis titik refleks untuk kesehatan organ internal dan
                        keseimbangan tubuh secara holistik.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 120.000</div>
                        <div class="service-duration">45 mnt</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-num">06</div>
                    <div class="service-name">Body Scrub</div>
                    <div class="service-desc">Eksfoliasi kulit menyeluruh dengan campuran garam laut, madu, dan minyak
                        alami pilihan terbaik.</div>
                    <div class="service-footer">
                        <div class="service-price">Rp 160.000</div>
                        <div class="service-duration">60 mnt</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROMOTIONS -->
    <section id="promotions">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Special Offers</div>
                <h2 class="section-title">Promo &amp; <em>Penawaran Spesial</em></h2>
                <p class="section-sub">Dapatkan penawaran terbaik untuk pengalaman spa yang tak terlupakan.</p>
            </div>
            <div class="promo-grid">
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#c4714b22;">🎁</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">Buy 4 Get 1</div>
                        <div class="promo-title">Paket Membership Eksklusif</div>
                        <div class="promo-desc">Pilihan paket membership KOICHI Family Reflexology dengan penawaran
                            harga terbaik yang menguntungkan.</div>
                    </div>
                </div>
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#8c5c3822;">🌙</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">Weekend Deal</div>
                        <div class="promo-title">Spesial Akhir Pekan</div>
                        <div class="promo-desc">Nikmati diskon spesial setiap Sabtu & Minggu untuk semua layanan pijat
                            dan spa pilihan.</div>
                    </div>
                </div>
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#b0785022;">🔥</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">Hot Deal</div>
                        <div class="promo-title">Paket Hemat Bulan Ini</div>
                        <div class="promo-desc">Hemat hingga 30% untuk pembelian paket treatment lengkap pilihan bulan
                            ini.</div>
                    </div>
                </div>
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#d4956e22;">✨</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">Facial</div>
                        <div class="promo-title">Perawatan Wajah Premium</div>
                        <div class="promo-desc">Facial lengkap dengan bahan organik premium — kulit bercahaya, lebih
                            muda, dan sehat alami.</div>
                    </div>
                </div>
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#7a453022;">💑</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">Couple</div>
                        <div class="promo-title">Couple Relaxation Package</div>
                        <div class="promo-desc">Nikmati relaksasi bersama pasangan dengan paket eksklusif dan ruangan
                            privat yang nyaman.</div>
                    </div>
                </div>
                <div class="promo-card fade-up">
                    <div class="promo-card-placeholder" style="background:#9b604022;">🌿</div>
                    <div class="promo-card-body">
                        <div class="promo-badge">New</div>
                        <div class="promo-title">Body Treatment Terbaru</div>
                        <div class="promo-desc">Rasakan sensasi body treatment dengan teknik terbaru untuk kulit lebih
                            lembut dan sehat.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RESERVATION -->
    <section id="reservation">
        <div class="section-inner">
            <div class="reservation-wrapper">
                <div class="fade-up">
                    <div class="section-eyebrow">Book Online</div>
                    <h2 class="section-title">Reservation<br><em>Without Registration</em></h2>
                    <p class="section-sub">Cukup isi formulir dan tim kami akan mengkonfirmasi jadwal Anda via WhatsApp
                        dalam 30 menit.</p>
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
                                    biaya tambahan.</div>
                            </div>
                        </div>
                        <div class="booking-feature">
                            <div class="booking-feature-icon">⏰</div>
                            <div>
                                <div class="booking-feature-title">Jam Fleksibel</div>
                                <div class="booking-feature-desc">Tersedia dari pukul 09.00–20.00 setiap hari termasuk
                                    hari libur nasional.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-form-card fade-up">
                    <h3 class="form-title">Make a Reservation</h3>
                    <p class="form-sub">Isi data di bawah ini. Tidak perlu membuat akun.</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" placeholder="Nama Anda">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. WhatsApp *</label>
                            <input type="tel" class="form-control" placeholder="08xx-xxxx-xxxx">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Layanan *</label>
                        <select class="form-control" id="serviceSelect">
                            <option value="">-- Pilih Layanan --</option>
                            <option value="1" data-duration="60">Swedish Massage — Rp 150.000 (60 mnt)</option>
                            <option value="2" data-duration="75">Aromaterapi — Rp 180.000 (75 mnt)</option>
                            <option value="3" data-duration="90">Hot Stone Therapy — Rp 220.000 (90 mnt)</option>
                            <option value="4" data-duration="60">Facial Spa — Rp 200.000 (60 mnt)</option>
                            <option value="5" data-duration="45">Refleksiologi — Rp 120.000 (45 mnt)</option>
                            <option value="6" data-duration="60">Body Scrub — Rp 160.000 (60 mnt)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilih Terapis (Opsional)</label>
                        <select class="form-control" id="bookingTherapistSelect">
                            <option value="">-- Terapis Mana Saja --</option>
                            <option value="1">Sari Dewi</option>
                            <option value="2">Anita Putri</option>
                            <option value="3">Bagas Pratama</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal *</label>
                        <input type="date" id="bookingDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilih Jam *</label>
                        <div id="slotArea">
                            <div class="slot-loading">Pilih terapis dan tanggal untuk melihat ketersediaan jam.</div>
                        </div>
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
                        <textarea class="form-control" rows="3" placeholder="Keluhan khusus, permintaan tertentu..."></textarea>
                    </div>
                    <button class="submit-btn" id="submitBtn" disabled>Pilih jam terlebih dahulu</button>
                    <p style="text-align:center;font-size:.72rem;color:var(--muted);margin-top:14px;line-height:1.6;">
                        Sudah punya akun? <a href="#" style="color:var(--terracotta);font-weight:600;">Masuk di
                            sini</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- GIFT CERTIFICATE -->
    <section id="gift">
        <div class="gift-bg-circle" style="width:600px;height:600px;top:-200px;right:-200px;"></div>
        <div class="gift-bg-circle" style="width:300px;height:300px;bottom:-100px;left:10%;"></div>
        <div class="section-inner">
            <div class="gift-inner">
                <div class="fade-up">
                    <div class="section-eyebrow" style="color:rgba(232,213,183,.55);">Gift for Someone Special</div>
                    <h2 class="gift-title">Berikan <em>Hadiah</em><br>Terbaik untuk<br>Orang Tersayang</h2>
                    <p class="gift-sub">Gift certificate Koichi Spa adalah hadiah sempurna untuk ulang tahun,
                        anniversary, atau momen spesial lainnya.</p>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:flex-start;gap:12px;min-width:140px;">
                            <span style="font-size:1.2rem;">🎁</span>
                            <div>
                                <div style="font-size:.8rem;font-weight:500;color:var(--sand);margin-bottom:3px;">Fisik
                                    & Digital</div>
                                <div
                                    style="font-size:.72rem;font-weight:300;color:rgba(255,255,255,.4);line-height:1.6;">
                                    Cetak atau kirim via WhatsApp</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:12px;min-width:140px;">
                            <span style="font-size:1.2rem;">⏳</span>
                            <div>
                                <div style="font-size:.8rem;font-weight:500;color:var(--sand);margin-bottom:3px;">
                                    Berlaku 1 Tahun</div>
                                <div
                                    style="font-size:.72rem;font-weight:300;color:rgba(255,255,255,.4);line-height:1.6;">
                                    Fleksibel digunakan kapan saja</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:12px;min-width:140px;">
                            <span style="font-size:1.2rem;">✨</span>
                            <div>
                                <div style="font-size:.8rem;font-weight:500;color:var(--sand);margin-bottom:3px;">Bisa
                                    Custom</div>
                                <div
                                    style="font-size:.72rem;font-weight:300;color:rgba(255,255,255,.4);line-height:1.6;">
                                    Personalisasi nama & nominal</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gift-card fade-up">
                    <div class="gift-card-title">Pilih Nominal Gift</div>
                    <div class="gift-card-sub">Pilih nominal atau isi sendiri sesuai keinginan Anda.</div>
                    <div class="gift-options" id="giftOptions">
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 100K</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 200K</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 350K</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 500K</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 750K</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                        <div class="gift-opt" onclick="selectGift(this)">
                            <div class="gift-opt-amount">Rp 1 Juta</div>
                            <div class="gift-opt-label">Gift</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Atau masukkan nominal</label>
                        <input type="text" class="form-control" id="giftCustom" placeholder="Rp. 0"
                            oninput="clearGiftOpt()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" class="form-control" placeholder="Nama penerima gift">
                    </div>
                    <button class="btn-gift">Pesan Gift Certificate →</button>
                </div>
            </div>
        </div>
    </section>

    <!-- LOCATION -->
    <section id="location">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Find Us</div>
                <h2 class="section-title">Lokasi &amp; <em>Jam Operasional</em></h2>
            </div>
            <div class="location-grid">
                <div class="location-map fade-up">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798!2d108.2787!3d-6.9208!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1sid!2sid!4v1"
                        loading="lazy" title="Peta"></iframe>
                </div>
                <div class="location-info fade-up">
                    <div class="location-item">
                        <div class="location-item-icon">📍</div>
                        <div>
                            <div class="location-item-label">Alamat</div>
                            <div class="location-item-value">Jl. Melati Raya No. 47, Cirebon, Jawa Barat</div>
                        </div>
                    </div>
                    <div class="location-item">
                        <div class="location-item-icon">📞</div>
                        <div>
                            <div class="location-item-label">Telepon / WhatsApp</div>
                            <div class="location-item-value">0821-5567-3894</div>
                        </div>
                    </div>
                    <div class="location-item">
                        <div class="location-item-icon">✉️</div>
                        <div>
                            <div class="location-item-label">Email</div>
                            <div class="location-item-value">hello@koichispa.id</div>
                        </div>
                    </div>
                    <div class="location-item">
                        <div class="location-item-icon">⏰</div>
                        <div>
                            <div class="location-item-label">Jam Operasional</div>
                            <div class="location-item-value">Senin – Minggu, 09.00 – 20.00<br>Termasuk hari libur
                                nasional</div>
                        </div>
                    </div>
                    <a href="https://wa.me/6282155673894" target="_blank"
                        style="display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:var(--terracotta);color:white;border-radius:4px;font-size:.75rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:background .2s;width:fit-content;"
                        onmouseover="this.style.background='var(--terra-dark)'"
                        onmouseout="this.style.background='var(--terracotta)'">
                        💬 Chat via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">Koichi <em>Spa</em></div>
                    <div class="footer-tagline">Wellness & Terapi Profesional. Hadir untuk memulihkan keseimbangan
                        tubuh dan pikiran Anda.</div>
                </div>
                <div class="footer-links">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="#about">About</a></li>
                        <li><a href="#treatments">Treatments</a></li>
                        <li><a href="#promotions">Promotions</a></li>
                        <li><a href="#reservation">Reservation</a></li>
                        <li><a href="#gift">Gift Certificate</a></li>
                        <li><a href="#location">Location</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Info</h4>
                    <ul>
                        <li><a href="#">Jam Operasional</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Admin Login</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Kontak</h4>
                    <ul>
                        <li><a href="#">📍 Jl. Melati Raya No. 47, Cirebon</a></li>
                        <li><a href="#">📞 0821-5567-3894</a></li>
                        <li><a href="#">✉ hello@koichispa.id</a></li>
                        <li><a href="#">⏰ 09.00 – 20.00</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 Koichi Spa. All rights reserved.</span>
                <span>Dibuat dengan ❤ untuk kesehatan Anda</span>
            </div>
        </div>
    </footer>

    <script>
        /* NAVBAR */
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 40), {
            passive: true
        });

        function toggleMenu() {
            const m = document.getElementById('mobileMenu'),
                b = document.getElementById('hamburger');
            b.setAttribute('aria-expanded', m.classList.toggle('open'));
        }

        function closeMenu() {
            document.getElementById('mobileMenu').classList.remove('open');
        }
        document.addEventListener('click', e => {
            const m = document.getElementById('mobileMenu'),
                b = document.getElementById('hamburger');
            if (m.classList.contains('open') && !m.contains(e.target) && !b.contains(e.target)) closeMenu();
        });

        /* SMOOTH SCROLL */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const t = document.querySelector(a.getAttribute('href'));
                if (!t) return;
                e.preventDefault();
                t.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        /* FADE-UP */
        const obs = new IntersectionObserver(entries => {
            entries.forEach(en => {
                if (en.isIntersecting) {
                    en.target.classList.add('visible');
                    obs.unobserve(en.target);
                }
            });
        }, {
            threshold: 0.08
        });
        document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));

        /* HERO CAROUSEL */
        (function() {
            const track = document.getElementById('heroTrack');
            const navEl = document.getElementById('heroNav');
            const counterEl = document.getElementById('heroCounter');
            const progressBar = document.getElementById('heroProgressBar');
            const slides = Array.from(track.querySelectorAll('.hero-slide'));
            const total = slides.length;
            let cur = 0,
                timer = null,
                paused = false,
                touchX = 0;

            slides.forEach((_, i) => {
                const d = document.createElement('div');
                d.className = 'hero-nav-dot' + (i === 0 ? ' active' : '');
                d.addEventListener('click', () => goTo(i));
                navEl.appendChild(d);
            });

            function updateProgress() {
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.transition = 'width 5s linear';
                    progressBar.style.width = '100%';
                }, 20);
            }

            function update() {
                track.style.transform = `translateX(-${cur * 100}%)`;
                counterEl.innerHTML =
                    `<strong>${String(cur+1).padStart(2,'0')}</strong> / ${String(total).padStart(2,'0')}`;
                slides.forEach((s, i) => s.classList.toggle('active', i === cur));
                navEl.querySelectorAll('.hero-nav-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
                updateProgress();
            }

            function goTo(idx) {
                cur = ((idx % total) + total) % total;
                update();
                clearInterval(timer);
                if (!paused) timer = setInterval(() => goTo(cur + 1), 5000);
            }

            document.getElementById('heroPrev').addEventListener('click', () => goTo(cur - 1));
            document.getElementById('heroNext').addEventListener('click', () => goTo(cur + 1));

            const hero = document.getElementById('hero');
            hero.addEventListener('touchstart', e => {
                touchX = e.touches[0].clientX;
                paused = true;
                clearInterval(timer);
            }, {
                passive: true
            });
            hero.addEventListener('touchend', e => {
                const dx = e.changedTouches[0].clientX - touchX;
                if (Math.abs(dx) > 40) goTo(cur + (dx < 0 ? 1 : -1));
                paused = false;
                goTo(cur);
            });
            hero.addEventListener('mouseenter', () => {
                paused = true;
                clearInterval(timer);
            });
            hero.addEventListener('mouseleave', () => {
                paused = false;
                goTo(cur);
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') goTo(cur - 1);
                if (e.key === 'ArrowRight') goTo(cur + 1);
            });

            update();
            timer = setInterval(() => goTo(cur + 1), 5000);
        })();

        /* GIFT */
        function selectGift(el) {
            document.querySelectorAll('.gift-opt').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            document.getElementById('giftCustom').value = '';
        }

        function clearGiftOpt() {
            document.querySelectorAll('.gift-opt').forEach(o => o.classList.remove('selected'));
        }

        /* SLOT PICKER (demo) */
        document.getElementById('bookingTherapistSelect')?.addEventListener('change', renderDemoSlots);
        document.getElementById('bookingDate')?.addEventListener('change', renderDemoSlots);

        function renderDemoSlots() {
            const tid = document.getElementById('bookingTherapistSelect').value;
            const date = document.getElementById('bookingDate').value;
            if (!tid || !date) {
                document.getElementById('slotArea').innerHTML =
                    '<div class="slot-loading">Pilih terapis dan tanggal untuk melihat ketersediaan jam.</div>';
                return;
            }
            const now = new Date();
            const fakeBooked = ['10:00', '13:00', '15:00'];
            const slots = [];
            for (let h = 9; h < 20; h++) slots.push(String(h).padStart(2, '0') + ':00');
            const html = `<div class="slot-grid">` + slots.map(slot => {
                const blocked = fakeBooked.includes(slot);
                const isPast = new Date(`${date}T${slot}`) <= now;
                const disabled = blocked || isPast;
                if (disabled)
                return `<button type="button" class="slot-btn" disabled><span style="display:block;font-size:.55rem;margin-bottom:1px;">${blocked ? '🔒' : '⏰'}</span>${slot}</button>`;
                return `<button type="button" class="slot-btn" onclick="pickSlot('${slot}')">${slot}</button>`;
            }).join('') + `</div>`;
            document.getElementById('slotArea').innerHTML = html;
        }

        function pickSlot(slot) {
            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
            event.target.classList.add('selected');
            const btn = document.getElementById('submitBtn');
            btn.disabled = false;
            btn.textContent = `Book Jam ${slot} →`;
        }
    </script>
</body>

</html>
