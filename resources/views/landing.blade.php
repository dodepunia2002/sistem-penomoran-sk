<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Penomoran Surat Keputusan (SK) berbasis web — Dinas Perhubungan Kabupaten Gianyar.">
    <meta property="og:title" content="Sistem Penomoran SK — Dishub Gianyar">
    <meta property="og:description" content="Platform penomoran Surat Keputusan (SK) resmi Dinas Perhubungan Kabupaten Gianyar.">
    <meta name="theme-color" content="#35348b">
    <title>Sistem Penomoran SK — Dinas Perhubungan Kabupaten Gianyar</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:      #35348b;
            --primary-dark: #1a1960;
            --accent:       #d39f28;
            --accent-light: #e6b33c;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #35348b 0%, #1a1960 50%, #0d0d3b 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* Particle background */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s ease-in-out infinite;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50%       { opacity: 0.7; transform: scale(1.4); }
        }

        /* Content */
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 560px;
            width: 100%;
        }

        /* Logo */
        .logo-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .logo-glow {
            position: absolute;
            inset: -20px;
            background: radial-gradient(circle, rgba(211,159,40,0.25) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50%       { opacity: 1; transform: scale(1.1); }
        }

        .logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            animation: float 4s ease-in-out infinite;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 8px 24px rgba(211,159,40,0.3));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        /* Title */
        h1 {
            font-size: 2.25rem;
            font-weight: 900;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
            background: linear-gradient(135deg, #fff 0%, #e8e6ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        h2 {
            font-size: 1.05rem;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            margin-bottom: 0.4rem;
        }

        .subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.4);
            margin-bottom: 2.5rem;
        }

        /* Divider */
        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            border-radius: 999px;
            margin: 1rem auto 2rem;
        }

        /* CTA Button */
        .btn-enter {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary-dark);
            padding: 1rem 2.75rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 8px 32px rgba(211,159,40,0.35), 0 2px 8px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .btn-enter::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-enter:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(211,159,40,0.45); }
        .btn-enter:hover::before { left: 100%; }

        .btn-enter svg { width: 18px; height: 18px; }

        /* Info chips */
        .info-chips {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
            margin-bottom: 2.5rem;
        }

        .chip {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.65);
            backdrop-filter: blur(8px);
        }

        /* Footer */
        .footer {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.25);
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 1rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 480px) {
            h1 { font-size: 1.6rem; }
            .logo { width: 90px; height: 90px; }
            .btn-enter { padding: 0.85rem 2rem; font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>

    <main class="content" role="main">
        <div class="logo-wrap">
            <div class="logo-glow"></div>
            <img src="{{ asset('logo-dishub.png') }}" alt="Logo Dinas Perhubungan Kabupaten Gianyar" class="logo">
        </div>

        <h1>SISTEM PENOMORAN SK</h1>
        <h2>Dinas Perhubungan Kabupaten Gianyar</h2>
        <p class="subtitle">Sistem Informasi Penomoran Surat Keputusan Berbasis Web</p>

        <div class="divider"></div>

        <div class="info-chips">
            <div class="chip">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Aman & Terenkripsi
            </div>
            <div class="chip">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Cepat & Efisien
            </div>
            <div class="chip">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Terdokumentasi
            </div>
        </div>

        <a href="{{ route('login') }}" class="btn-enter">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Masuk Sistem
        </a>

        <p class="footer">© {{ date('Y') }} Dinas Perhubungan Kabupaten Gianyar — Powered by Laravel</p>
    </main>

    <script>
        // Generate twinkling particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 60; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 2.5 + 0.5;
            p.style.cssText = [
                `width:${size}px`,
                `height:${size}px`,
                `left:${Math.random()*100}%`,
                `top:${Math.random()*100}%`,
                `animation-delay:${Math.random()*4}s`,
                `animation-duration:${Math.random()*3+2}s`,
            ].join(';');
            container.appendChild(p);
        }
    </script>
</body>
</html>
