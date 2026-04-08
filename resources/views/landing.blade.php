<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Baca — Digital Library</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --green-deep: #0d3d2e;
            --green-mid: #166534;
            --green-bright: #22c55e;
            --green-light: #bbf7d0;
            --cream: #faf7f2;
            --warm-white: #fffef9;
            --ink: #0f1a14;
            --muted: #6b7c6e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3 {
            font-family: 'Playfair Display', serif;
        }

        /* ── NOISE TEXTURE OVERLAY ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 9999;
            opacity: 0.4;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 20px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.4s ease;
        }

        .navbar.scrolled {
            background: rgba(250, 247, 242, 0.92);
            backdrop-filter: blur(16px);
            padding: 14px 48px;
            border-bottom: 1px solid rgba(22, 101, 52, 0.1);
        }

        /* ── LOGO STYLE ── */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: var(--green-mid);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.5px;
        }

        .logo-ruang {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.3px;
        }

        .logo-baca {
            font-size: 16px;
            font-weight: 700;
            color: var(--green-mid);
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--green-mid);
        }

        .nav-links {
            display: flex;
            gap: 36px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--muted);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.3px;
            transition: color 0.2s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: var(--green-mid);
            transition: width 0.3s;
        }

        .nav-links a:hover {
            color: var(--green-mid);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-deep);
            color: #fff;
            padding: 11px 24px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            letter-spacing: 0.2px;
        }

        .nav-cta:hover {
            background: var(--green-mid);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(22, 101, 52, 0.25);
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 0 48px;
        }

        /* Animated background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: blobFloat 8s ease-in-out infinite;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #22c55e, #166534);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #bbf7d0, #4ade80);
            bottom: 50px;
            left: -80px;
            animation-delay: -3s;
        }

        .blob-3 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, #166534, #0d3d2e);
            top: 40%;
            left: 30%;
            animation-delay: -5s;
            opacity: 0.1;
        }

        @keyframes blobFloat {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(20px, -30px) scale(1.05);
            }

            66% {
                transform: translate(-15px, 20px) scale(0.95);
            }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--green-bright);
            border-radius: 50%;
            opacity: 0;
            animation: particleRise linear infinite;
        }

        @keyframes particleRise {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-20vh) scale(1.5);
                opacity: 0;
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.25);
            color: var(--green-mid);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 28px;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.3s forwards;
        }

        .hero-title {
            font-size: clamp(52px, 7vw, 96px);
            font-weight: 900;
            line-height: 1.02;
            letter-spacing: -3px;
            color: var(--ink);
            margin-bottom: 24px;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.5s forwards;
        }

        .hero-title .highlight {
            color: var(--green-mid);
            font-style: italic;
            position: relative;
        }

        .hero-title .highlight::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--green-bright);
            border-radius: 2px;
            opacity: 0.4;
        }

        .hero-sub {
            font-size: 17px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 560px;
            margin: 0 auto 40px;
            font-weight: 300;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.7s forwards;
        }

        .hero-btns {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 64px;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.9s forwards;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--green-deep);
            color: white;
            padding: 16px 32px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 20px rgba(13, 61, 46, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 40px rgba(13, 61, 46, 0.4);
            background: var(--green-mid);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: transparent;
            color: var(--green-deep);
            padding: 16px 32px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid rgba(13, 61, 46, 0.2);
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            border-color: var(--green-mid);
            background: rgba(34, 197, 94, 0.06);
            transform: translateY(-2px);
        }

        /* ── BOOK ANIMATION ── */
        .book-scene {
            position: relative;
            width: 280px;
            height: 200px;
            margin: 0 auto;
            perspective: 1200px;
            opacity: 0;
            animation: fadeUp 1s ease 1.1s forwards;
        }

        .book {
            position: relative;
            width: 200px;
            height: 160px;
            margin: 0 auto;
            transform-style: preserve-3d;
            transform: rotateY(-20deg) rotateX(10deg);
            animation: bookFloat 4s ease-in-out infinite, bookReveal 1.5s cubic-bezier(0.34, 1.56, 0.64, 1) 1.3s both;
        }

        @keyframes bookReveal {
            from {
                transform: rotateY(-90deg) rotateX(20deg) scale(0.5);
                opacity: 0;
            }

            to {
                transform: rotateY(-20deg) rotateX(10deg) scale(1);
                opacity: 1;
            }
        }

        @keyframes bookFloat {

            0%,
            100% {
                transform: rotateY(-20deg) rotateX(10deg) translateY(0);
            }

            50% {
                transform: rotateY(-15deg) rotateX(8deg) translateY(-12px);
            }
        }

        .book-cover {
            position: absolute;
            width: 160px;
            height: 200px;
            background: linear-gradient(135deg, #166534 0%, #0d3d2e 60%, #052e16 100%);
            border-radius: 4px 16px 16px 4px;
            transform-origin: left center;
            transform-style: preserve-3d;
            animation: openBook 3s cubic-bezier(0.45, 0.05, 0.55, 0.95) 2s infinite alternate;
            box-shadow: 6px 6px 30px rgba(0, 0, 0, 0.35), -2px 0 8px rgba(0, 0, 0, 0.2);
        }

        @keyframes openBook {
            0% {
                transform: rotateY(0deg);
            }

            40%,
            60% {
                transform: rotateY(-35deg);
            }

            100% {
                transform: rotateY(0deg);
            }
        }

        .book-cover-front {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            backface-visibility: hidden;
        }

        .book-cover-icon {
            font-size: 36px;
            color: rgba(255, 255, 255, 0.9);
        }

        .book-cover-title {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 0 12px;
            font-weight: 700;
            line-height: 1.3;
        }

        .book-cover-line {
            width: 40px;
            height: 1.5px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 1px;
        }

        .book-spine {
            position: absolute;
            left: 0;
            top: 0;
            width: 22px;
            height: 200px;
            background: linear-gradient(180deg, #0d3d2e, #052e16);
            border-radius: 4px 0 0 4px;
            transform: rotateY(-90deg) translateZ(11px) translateX(-11px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-spine-text {
            font-family: 'Playfair Display', serif;
            font-size: 9px;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
            writing-mode: vertical-rl;
        }

        .book-back {
            position: absolute;
            width: 160px;
            height: 200px;
            background: linear-gradient(135deg, #052e16, #0d3d2e);
            border-radius: 4px 16px 16px 4px;
            transform: translateZ(-8px);
        }

        /* Inner pages effect */
        .book-pages {
            position: absolute;
            left: 22px;
            top: 4px;
            width: 130px;
            height: 192px;
            background: linear-gradient(90deg, #e8f5e9 0%, #f5f5f5 40%, #fff 100%);
            border-radius: 0 12px 12px 0;
            transform: translateZ(-4px);
            overflow: hidden;
        }

        .book-pages::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background: repeating-linear-gradient(transparent, transparent 11px, rgba(22, 101, 52, 0.07) 11px, rgba(22, 101, 52, 0.07) 12px);
        }

        /* Flying page animation */
        .flying-page {
            position: absolute;
            width: 120px;
            height: 160px;
            background: linear-gradient(135deg, #f0fdf4, #fff);
            border-radius: 4px 10px 10px 4px;
            transform-origin: left center;
            animation: pageFlip 3s cubic-bezier(0.45, 0.05, 0.55, 0.95) 2s infinite alternate;
            left: 22px;
            top: 20px;
            box-shadow: 4px 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .flying-page::before {
            content: '';
            position: absolute;
            inset: 8px;
            background: repeating-linear-gradient(transparent, transparent 13px, rgba(22, 101, 52, 0.12) 13px, rgba(22, 101, 52, 0.12) 14px);
        }

        @keyframes pageFlip {

            0%,
            100% {
                transform: rotateY(0deg);
                opacity: 0;
            }

            15%,
            85% {
                opacity: 1;
            }

            40%,
            60% {
                transform: rotateY(-50deg);
                opacity: 1;
            }
        }

        /* Sparkles around book */
        .sparkle {
            position: absolute;
            pointer-events: none;
            animation: sparkleAnim 2s ease-in-out infinite;
        }

        @keyframes sparkleAnim {

            0%,
            100% {
                transform: scale(0) rotate(0deg);
                opacity: 0;
            }

            50% {
                transform: scale(1) rotate(180deg);
                opacity: 1;
            }
        }

        /* Stats row */
        .stats-row {
            display: flex;
            gap: 40px;
            justify-content: center;
            margin-top: 48px;
            opacity: 0;
            animation: fadeUp 0.8s ease 1.3s forwards;
        }

        .stat-item {
            text-align: center;
        }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 900;
            color: var(--green-mid);
            line-height: 1;
            display: block;
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .stat-divider {
            width: 1px;
            background: rgba(22, 101, 52, 0.15);
            align-self: stretch;
        }

        /* Scroll indicator */
        .scroll-hint {
            position: absolute;
            bottom: 36px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: scrollBounce 2s ease infinite, fadeIn 1s ease 2s both;
        }

        .scroll-mouse {
            width: 22px;
            height: 36px;
            border: 2px solid rgba(22, 101, 52, 0.3);
            border-radius: 11px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .scroll-wheel {
            width: 3px;
            height: 7px;
            background: var(--green-mid);
            border-radius: 2px;
            animation: wheelScroll 1.8s ease infinite;
        }

        @keyframes wheelScroll {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            100% {
                transform: translateY(10px);
                opacity: 0;
            }
        }

        @keyframes scrollBounce {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(6px);
            }
        }

        /* ── SECTION: HOW IT WORKS ── */
        .section-how {
            padding: 120px 48px;
            background: var(--warm-white);
            position: relative;
            overflow: hidden;
        }

        .section-how::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(22, 101, 52, 0.2), transparent);
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--green-mid);
            margin-bottom: 16px;
            display: block;
        }

        .section-title {
            font-size: clamp(36px, 4vw, 56px);
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1.08;
            color: var(--ink);
            margin-bottom: 20px;
        }

        .section-sub {
            font-size: 16px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 500px;
            font-weight: 300;
        }

        .how-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            max-width: 1100px;
            margin: 80px auto 0;
        }

        .how-steps {
            display: flex;
            flex-direction: column;
            gap: 36px;
        }

        .how-step {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            opacity: 0;
            transform: translateX(-30px);
            transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .how-step.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .step-num {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: var(--green-deep);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
        }

        .step-content h3 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            color: var(--ink);
        }

        .step-content p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        .how-visual {
            position: relative;
            height: 500px;
        }

        /* ── SECTION: FEATURES ── */
        .section-features {
            padding: 120px 48px;
            background: var(--green-deep);
            position: relative;
            overflow: hidden;
        }

        .section-features::after {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.08), transparent 70%);
            pointer-events: none;
        }

        .features-header {
            text-align: center;
            margin-bottom: 72px;
        }

        .features-header .section-label {
            color: var(--green-bright);
        }

        .features-header .section-title {
            color: white;
        }

        .features-header .section-sub {
            color: rgba(255, 255, 255, 0.5);
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            max-width: 1100px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 24px;
            overflow: hidden;
        }

        .feature-card {
            padding: 40px 36px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            transition: opacity 0.7s ease, transform 0.7s ease, background 0.3s ease;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.06), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card:nth-child(3n) {
            border-right: none;
        }

        .feature-card:nth-child(n+4) {
            border-bottom: none;
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--green-bright);
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .feature-card:hover .feature-icon {
            background: rgba(34, 197, 94, 0.2);
            transform: scale(1.08) rotate(-3deg);
        }

        .feature-card h3 {
            font-size: 17px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .feature-card p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.65;
        }

        /* ── SECTION: STATS ── */
        .section-stats {
            padding: 100px 48px;
            background: var(--cream);
            position: relative;
        }

        .stats-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 2px;
            background: rgba(22, 101, 52, 0.1);
            border-radius: 24px;
            overflow: hidden;
        }

        .stat-block {
            background: var(--warm-white);
            padding: 48px 32px;
            text-align: center;
            position: relative;
            opacity: 0;
            transition: all 0.7s ease;
        }

        .stat-block.visible {
            opacity: 1;
        }

        .stat-block-num {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 900;
            color: var(--green-mid);
            line-height: 1;
            letter-spacing: -3px;
            display: block;
            margin-bottom: 8px;
        }

        .stat-block-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── SECTION: CTA ── */
        .section-cta {
            padding: 120px 48px;
            background: var(--warm-white);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-bg-text {
            position: absolute;
            font-family: 'Playfair Display', serif;
            font-size: 200px;
            font-weight: 900;
            color: rgba(22, 101, 52, 0.04);
            white-space: nowrap;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            letter-spacing: -10px;
            user-select: none;
        }

        .cta-inner {
            position: relative;
            z-index: 2;
        }

        .cta-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: var(--green-mid);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .cta-title {
            font-size: clamp(40px, 5vw, 72px);
            font-weight: 900;
            letter-spacing: -3px;
            line-height: 1.05;
            color: var(--ink);
            max-width: 720px;
            margin: 0 auto 24px;
        }

        .cta-sub {
            font-size: 17px;
            color: var(--muted);
            max-width: 480px;
            margin: 0 auto 48px;
            line-height: 1.7;
            font-weight: 300;
        }

        .cta-btns {
            display: flex;
            gap: 14px;
            justify-content: center;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--green-deep);
            color: rgba(255, 255, 255, 0.5);
            padding: 64px 48px 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            max-width: 1100px;
            margin: 0 auto 48px;
        }

        .footer-brand .logo-text {
            color: white;
        }

        .footer-desc {
            font-size: 14px;
            line-height: 1.7;
            margin-top: 16px;
            font-weight: 300;
        }

        .footer-col h4 {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--green-bright);
        }

        .footer-bottom {
            max-width: 1100px;
            margin: 0 auto;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .footer-socials {
            display: flex;
            gap: 16px;
        }

        .footer-socials a {
            width: 34px;
            height: 34px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .footer-socials a:hover {
            border-color: var(--green-bright);
            color: var(--green-bright);
            background: rgba(34, 197, 94, 0.08);
        }

        /* ── MOBILE MENU ── */
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: var(--ink);
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── MARQUEE ── */
        .marquee-section {
            padding: 24px 0;
            background: var(--green-deep);
            overflow: hidden;
            position: relative;
        }

        .marquee-track {
            display: flex;
            gap: 48px;
            animation: marquee 20s linear infinite;
            width: max-content;
        }

        .marquee-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .marquee-item i {
            color: var(--green-bright);
            font-size: 10px;
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .navbar {
                padding: 16px 24px;
            }

            .navbar.scrolled {
                padding: 12px 24px;
            }

            .nav-links,
            .nav-cta {
                display: none;
            }

            .hamburger {
                display: block;
            }

            /* Mobile Menu Classes Toggled via JS */
            .nav-links.nav-mobile-active {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: var(--cream);
                padding: 24px;
                gap: 20px;
                border-bottom: 1px solid rgba(22, 101, 52, 0.1);
                z-index: 99;
            }

            .nav-cta.nav-mobile-active {
                display: inline-flex;
                position: fixed;
                top: calc(70px + 230px);
                left: 24px;
                right: 24px;
                justify-content: center;
                z-index: 99;
            }

            .hero {
                padding: 100px 24px 60px;
            }

            .hero-title {
                letter-spacing: -2px;
            }

            .stats-row {
                gap: 24px;
            }

            .stat-num {
                font-size: 28px;
            }

            .section-how,
            .section-features,
            .section-stats,
            .section-cta {
                padding: 80px 24px;
            }

            .how-grid {
                grid-template-columns: 1fr;
                gap: 48px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .feature-card:nth-child(n) {
                border-right: none;
            }

            .stats-inner {
                grid-template-columns: 1fr 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="particles" id="particles"></div>

    <nav class="navbar" id="navbar">
        <a href="{{ route('landing') }}" class="logo">
            <div class="logo-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Ruang Baca Logo" class="logo-img">
            </div>
            <div class="logo-text">
                <span class="logo-ruang">RUANG</span>
                <span class="logo-baca">BACA</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#cara-kerja">Cara Kerja</a></li>
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#kontak">Kontak</a></li>
        </ul>
        <a href="{{ route('login') }}" class="nav-cta">
            Masuk Pustaka <i class="fas fa-arrow-right" style="font-size:11px"></i>
        </a>
        <button class="hamburger" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
    </nav>

    <section id="beranda" class="hero">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="hero-content">
            {{-- <div class="hero-eyebrow">
                <i class="fas fa-graduation-cap" style="font-size:10px"></i>
                Sistem Perpustakaan Digital
            </div> --}}

            <h1 class="hero-title mt-10">
                Buka Halaman,<br>
                Buka <span class="highlight">Masa Depan.</span>
            </h1>

            <p class="hero-sub">
                Platform perpustakaan digital terpadu untuk sekolah modern —
                menghubungkan siswa dengan ribuan koleksi buku kapan saja, di mana saja.
            </p>

            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-book-open" style="font-size:14px"></i>
                    Jelajahi Koleksi
                </a>
                <a href="#cara-kerja" class="btn-secondary">
                    Pelajari Lebih <i class="fas fa-chevron-down" style="font-size:12px"></i>
                </a>
            </div>

            <div class="book-scene">
                <div class="book" id="hero-book">
                    <div class="book-back"></div>
                    <div class="book-pages"></div>
                    <div class="flying-page"></div>
                    <div class="book-cover">
                        <div class="book-cover-front">
                            <div class="book-cover-icon"><i class="fas fa-book-open"></i></div>
                            <div class="book-cover-line"></div>
                            <div class="book-cover-title">Ruang Baca<br>Digital Library</div>
                        </div>
                    </div>
                    <div class="book-spine"><span class="book-spine-text">Ruang Baca</span></div>
                </div>

                <svg class="sparkle" style="width:16px;top:10px;right:20px;animation-delay:0.3s" viewBox="0 0 16 16"
                    fill="none">
                    <path d="M8 0L9.5 6.5L16 8L9.5 9.5L8 16L6.5 9.5L0 8L6.5 6.5Z" fill="#22c55e" opacity="0.7" />
                </svg>
                <svg class="sparkle" style="width:10px;top:60px;left:10px;animation-delay:1s" viewBox="0 0 16 16"
                    fill="none">
                    <path d="M8 0L9.5 6.5L16 8L9.5 9.5L8 16L6.5 9.5L0 8L6.5 6.5Z" fill="#4ade80" opacity="0.8" />
                </svg>
                <svg class="sparkle" style="width:12px;bottom:20px;right:10px;animation-delay:0.7s" viewBox="0 0 16 16"
                    fill="none">
                    <path d="M8 0L9.5 6.5L16 8L9.5 9.5L8 16L6.5 9.5L0 8L6.5 6.5Z" fill="#86efac" opacity="0.6" />
                </svg>
            </div>

            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-num">{{ number_format($total_books) }}+</span>
                    <div class="stat-label">Koleksi Buku</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-num">{{ number_format($total_students) }}+</span>
                    <div class="stat-label">Anggota Aktif</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-num">{{ $total_schools }}+</span>
                    <div class="stat-label">Sekolah</div>
                </div>
            </div>
        </div>

        <div class="scroll-hint">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            {{-- <span>Scroll</span> --}}
        </div>
    </section>

    <div class="marquee-section">
        <div class="marquee-track">
            @foreach (range(1, 2) as $_)
                <div class="marquee-item"><i class="fas fa-circle"></i> Katalog Digital</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Peminjaman Online</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Multi-Sekolah</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Notifikasi Otomatis</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Manajemen Anggota</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Laporan Real-Time</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Approval System</div>
                <div class="marquee-item"><i class="fas fa-circle"></i> Sistem Denda Otomatis</div>
            @endforeach
        </div>
    </div>

    <section id="cara-kerja" class="section-how">
        <div style="max-width:1100px;margin:0 auto">
            <div class="reveal">
                <span class="section-label">Cara Kerja</span>
                <h2 class="section-title">Sederhana, Cepat,<br>& Terpercaya.</h2>
                <p class="section-sub">Dari permintaan pinjam hingga pengembalian — semua terkontrol dengan sistem
                    persetujuan berlapis.</p>
            </div>

            <div class="how-grid">
                <div class="how-steps">
                    <div class="how-step" data-delay="0">
                        <div class="step-num">1</div>
                        <div class="step-content">
                            <h3>Cari & Pilih Buku</h3>
                            <p>Jelajahi katalog lengkap, filter berdasarkan kategori, penulis, atau rating. Temukan buku
                                yang sempurna.</p>
                        </div>
                    </div>
                    <div class="how-step" data-delay="150">
                        <div class="step-num">2</div>
                        <div class="step-content">
                            <h3>Ajukan Permintaan</h3>
                            <p>Klik tombol pinjam dan pengajuanmu langsung dikirim ke pustakawan untuk mendapat
                                persetujuan.</p>
                        </div>
                    </div>
                    <div class="how-step" data-delay="300">
                        <div class="step-num">3</div>
                        <div class="step-content">
                            <h3>Tunggu ACC Admin</h3>
                            <p>Pustakawan memverifikasi stok dan menyetujui permintaanmu. Kamu dapat notifikasi
                                langsung.</p>
                        </div>
                    </div>
                    <div class="how-step" data-delay="450">
                        <div class="step-num">4</div>
                        <div class="step-content">
                            <h3>Baca & Kembalikan</h3>
                            <p>Nikmati membaca! Saat selesai, ajukan pengembalian melalui aplikasi — pustakawan akan
                                mengkonfirmasi.</p>
                        </div>
                    </div>
                </div>

                <div class="how-visual">
                    <div id="book-stack-viz"
                        style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                        <div
                            style="width:280px;height:480px;background:var(--green-deep);border-radius:32px;padding:24px;position:relative;box-shadow:0 40px 80px rgba(13,61,46,0.3);">
                            <div
                                style="background:rgba(255,255,255,0.04);border-radius:20px;height:100%;overflow:hidden;padding:20px;">
                                <div
                                    style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                    <div
                                        style="font-family:'Playfair Display',serif;color:white;font-size:15px;font-weight:700;">
                                        Katalog Buku</div>
                                    <div
                                        style="width:28px;height:28px;background:rgba(34,197,94,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-search" style="color:#22c55e;font-size:11px;"></i>
                                    </div>
                                </div>

                                @php
                                    $mockBooks = [
                                        ['Matematika Kelas XII', 'Ali Sudrajat', '#166534'],
                                        ['Fisika Modern', 'Budi Santoso', '#0d3d2e'],
                                        ['Bahasa Indonesia', 'Siti Rahayu', '#1a4a38'],
                                        ['Kimia Dasar', 'Ahmad Fauzi', '#0f3529'],
                                    ];
                                @endphp
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    @foreach ($mockBooks as $i => $mb)
                                        <div
                                            style="display:flex;align-items:center;gap:12px;background:rgba(255,255,255,0.04);border-radius:12px;padding:12px;border:1px solid rgba(255,255,255,0.06);animation:fadeUp 0.5s ease {{ $i * 0.15 + 1.2 }}s both;">
                                            <div
                                                style="width:36px;height:48px;background:{{ $mb[2] }};border-radius:6px;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-book"
                                                    style="color:rgba(255,255,255,0.5);font-size:12px;"></i>
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <div
                                                    style="font-size:12px;font-weight:600;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    {{ $mb[0] }}</div>
                                                <div
                                                    style="font-size:10px;color:rgba(255,255,255,0.35);margin-top:2px;">
                                                    {{ $mb[1] }}</div>
                                                <div style="display:flex;gap:4px;margin-top:6px;">
                                                    <div
                                                        style="height:4px;width:40px;background:rgba(34,197,94,0.2);border-radius:2px;">
                                                    </div>
                                                    <div
                                                        style="height:4px;width:24px;background:rgba(34,197,94,0.1);border-radius:2px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                style="width:28px;height:28px;background:rgba(34,197,94,0.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="fas fa-plus" style="color:#22c55e;font-size:10px;"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div
                                    style="margin-top:16px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;">
                                    <div
                                        style="width:8px;height:8px;background:#22c55e;border-radius:50%;animation:pulse 1.5s infinite;">
                                    </div>
                                    <span style="font-size:11px;color:rgba(255,255,255,0.6);">2 buku menunggu ACC
                                        admin</span>
                                </div>
                            </div>

                            <div
                                style="position:absolute;bottom:12px;left:50%;transform:translateX(-50%);width:40px;height:4px;background:rgba(255,255,255,0.15);border-radius:2px;">
                            </div>
                        </div>

                        <div style="position:absolute;top:20px;right:10px;display:flex;flex-direction:column;gap:6px;">
                            @for ($i = 0; $i < 6; $i++)
                                <div style="width:4px;height:4px;background:rgba(22,101,52,0.25);border-radius:50%;">
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="section-features">
        <div class="features-header reveal">
            <span class="section-label">Fitur Unggulan</span>
            <h2 class="section-title">Semua yang Kamu<br>Butuhkan.</h2>
            <p class="section-sub" style="margin-top:16px">Dirancang untuk kemudahan siswa dan efisiensi pustakawan
                dalam satu platform terpadu.</p>
        </div>

        <div class="features-grid">
            @php
                $features = [
                    [
                        'fas fa-book',
                        'Katalog Digital',
                        'Ribuan koleksi buku tersedia dan dapat dicari dengan mudah berdasarkan judul, penulis, atau ISBN.',
                    ],
                    [
                        'fas fa-check-double',
                        'Sistem Persetujuan',
                        'Setiap peminjaman dan pengembalian melewati verifikasi pustakawan untuk kontrol yang lebih baik.',
                    ],
                    [
                        'fas fa-bell',
                        'Notifikasi Real-Time',
                        'Siswa dan admin mendapat notifikasi langsung untuk setiap update status peminjaman.',
                    ],
                    [
                        'fas fa-heart',
                        'Wishlist Buku',
                        'Simpan buku favorit ke wishlist dan dapatkan info saat buku tersedia.',
                    ],
                    [
                        'fas fa-star',
                        'Ulasan & Rating',
                        'Baca dan tulis ulasan untuk membantu sesama siswa memilih buku terbaik.',
                    ],
                    [
                        'fas fa-coins',
                        'Denda Otomatis',
                        'Sistem denda keterlambatan dihitung otomatis sesuai kebijakan sekolah masing-masing.',
                    ],
                ];
            @endphp
            @foreach ($features as $i => $f)
                <div class="feature-card" data-delay="{{ $i * 80 }}">
                    <div class="feature-icon"><i class="{{ $f[0] }}"></i></div>
                    <h3>{{ $f[1] }}</h3>
                    <p>{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section-stats">
        <div class="reveal" style="text-align:center;margin-bottom:60px">
            <span class="section-label">Dalam Angka</span>
            <h2 class="section-title">Dipercaya Ribuan<br>Siswa Indonesia.</h2>
        </div>
        <div class="stats-inner">
            @php
                $statBlocks = [
                    [$total_books . '+', 'Koleksi Buku'],
                    [$total_students . '+', 'Anggota Aktif'],
                    [$total_schools . '+', 'Sekolah Terdaftar'],
                    ['99%', 'Tingkat Kepuasan'],
                ];
            @endphp
            @foreach ($statBlocks as $i => $s)
                <div class="stat-block" data-delay="{{ $i * 100 }}">
                    <span class="stat-block-num">{{ $s[0] }}</span>
                    <div class="stat-block-label">{{ $s[1] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section-cta">
        <div class="cta-bg-text">BACA</div>
        <div class="cta-inner reveal">
            <div class="cta-badge">
                <i class="fas fa-rocket" style="font-size:10px"></i>
                Mulai Sekarang — Gratis
            </div>
            <h2 class="cta-title">Siap Modernisasi Perpustakaan Sekolahmu?</h2>
            <p class="cta-sub">Bergabunglah dengan ratusan sekolah dan rasakan kemudahan mengelola literasi generasi
                masa depan.</p>
            <div class="cta-btns">
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-book-open" style="font-size:14px"></i>
                    Coba Demo Gratis
                </a>
                <a href="#kontak" class="btn-secondary">
                    Hubungi Kami <i class="fas fa-arrow-right" style="font-size:12px"></i>
                </a>
            </div>
        </div>
    </section>

    <footer id="kontak">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-7 h-7 object-contain">
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-white text-xL font-bold tracking-wider">RUANG</span>
                        <span class="text-white text-xl font-bold tracking-tight">BACA</span>
                    </div>
                </a>
                <p class="footer-desc">Platform perpustakaan digital terpercaya untuk sekolah modern di Indonesia.
                    Menghubungkan siswa dengan pengetahuan tanpa batas.</p>
            </div>
            <div class="footer-col">
                <h4>Platform</h4>
                <ul class="footer-links">
                    <li><a href="#">Katalog Buku</a></li>
                    <li><a href="#">Manajemen Anggota</a></li>
                    <li><a href="#">Laporan & Analitik</a></li>
                    <li><a href="#">Pengaturan Sekolah</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Perusahaan</h4>
                <ul class="footer-links">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Karir</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kontak</h4>
                <ul class="footer-links">
                    <li><a href="#">hello@ruangbaca.com</a></li>
                    <li><a href="#">+62 812-3456-7890</a></li>
                    <li><a href="#">Jakarta Selatan, Indonesia</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} RUANG BACA. All rights reserved.</span>
            <div class="footer-socials">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </footer>

    <script>
        // ── Particles ──────────────────────────────────────────────────
        (function() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 18; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.cssText = `
                    left: ${Math.random() * 100}%;
                    width: ${Math.random() * 3 + 2}px;
                    height: ${Math.random() * 3 + 2}px;
                    animation-duration: ${Math.random() * 12 + 8}s;
                    animation-delay: ${Math.random() * 10}s;
                    opacity: 0;
                `;
                container.appendChild(p);
            }
        })();

        // ── Navbar scroll ──────────────────────────────────────────────
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        }, {
            passive: true
        });

        // ── Intersection Observer for reveals ─────────────────────────
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, parseInt(delay));
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -60px 0px'
        });

        document.querySelectorAll('.reveal, .how-step, .feature-card, .stat-block').forEach(el => {
            observer.observe(el);
        });

        // ── Parallax on scroll ─────────────────────────────────────────
        const blobs = document.querySelectorAll('.blob');
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            blobs.forEach((b, i) => {
                const speed = [0.15, -0.1, 0.08][i] || 0;
                b.style.transform = `translateY(${y * speed}px)`;
            });
        }, {
            passive: true
        });

        // ── Mobile menu ────────────────────────────────────────────────
        function toggleMenu() {
            const links = document.querySelector('.nav-links');
            const cta = document.querySelector('.nav-cta');
            links.classList.toggle('nav-mobile-active');
            cta.classList.toggle('nav-mobile-active');
        }

        // ── Smooth anchor scroll ───────────────────────────────────────
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // ── Book tilt on mouse move ────────────────────────────────────
        const book = document.getElementById('hero-book');
        if (book) {
            document.addEventListener('mousemove', (e) => {
                if (window.scrollY > window.innerHeight * 0.5) return;
                const cx = window.innerWidth / 2;
                const cy = window.innerHeight / 2;
                const dx = (e.clientX - cx) / cx;
                const dy = (e.clientY - cy) / cy;
                book.style.transform = `rotateY(${-20 + dx * 8}deg) rotateX(${10 - dy * 5}deg)`;
            });
        }

        // ── Count-up animation for stats ──────────────────────────────
        function countUp(el, target, duration = 1800) {
            const start = performance.now();
            const isNum = !isNaN(target.replace(/[^0-9]/g, ''));
            if (!isNum) return;
            const num = parseInt(target.replace(/[^0-9]/g, ''));
            const suffix = target.replace(/[0-9,]/g, '');
            const animate = (now) => {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(ease * num);
                el.textContent = current.toLocaleString('id-ID') + suffix;
                if (progress < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        }

        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const numEl = entry.target.querySelector('.stat-block-num');
                    if (numEl) countUp(numEl, numEl.textContent);
                    statObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        document.querySelectorAll('.stat-block').forEach(el => statObserver.observe(el));
    </script>
</body>

</html>
