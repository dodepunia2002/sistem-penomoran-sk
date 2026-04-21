<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Penomoran Surat Keputusan (SK) — Dinas Perhubungan Kabupaten Gianyar">
    <title>{{ $title ?? 'Sistem Penomoran SK' }} — Dishub Gianyar</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Design Tokens ─────────────────────────────────── */
        :root {
            --primary:        #35348b;
            --primary-light:  #484793;
            --primary-dark:   #27267a;
            --accent:         #d39f28;
            --accent-light:   #e6b33c;
            --surface:        #f4f5f7;
            --surface-card:   #ffffff;
            --text-primary:   #111827;
            --text-muted:     #6b7280;
            --border:         #e5e7eb;
            --sidebar-w:      240px;
            --sidebar-w-mob:  280px;
            --radius:         1rem;
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.06);
            --shadow-md:      0 4px 15px rgba(0,0,0,0.07);
            --shadow-lg:      0 10px 30px rgba(0,0,0,0.1);
            --transition:     0.25s cubic-bezier(0.4,0,0.2,1);
        }

        /* ── Reset & Base ───────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--surface);
            color: var(--text-primary);
            font-size: 0.9375rem;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar ───────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            padding: 1.75rem 0 1.25rem;
            z-index: 1000;
            border-radius: 0 1.25rem 1.25rem 0;
            box-shadow: 4px 0 20px rgba(53,52,139,0.18);
            transition: transform var(--transition);
            overflow: hidden;
        }

        .sidebar-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 0 1rem 1.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 0.75rem;
        }

        .sidebar-logo-wrap {
            width: 68px;
            height: 68px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            padding: 4px;
        }

        .sidebar-logo-wrap img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .sidebar-header h3 {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1.4;
            color: rgba(255,255,255,0.85);
        }

        /* Nav links */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            padding: 0 0.75rem;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-radius: 0.75rem;
            border-left: 3px solid transparent;
            transition: all var(--transition);
            letter-spacing: 0.04em;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        .sidebar-link.active {
            background: rgba(255,255,255,0.12);
            color: var(--accent-light);
            border-left-color: var(--accent);
        }

        .sidebar-link svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.8;
        }

        .sidebar-link.active svg,
        .sidebar-link:hover svg { opacity: 1; }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 1rem 1.25rem 0;
            margin-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 0.85rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary-dark);
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.9);
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.65rem;
            color: var(--accent-light);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239,68,68,0.12);
            color: rgba(255,255,255,0.7);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 0.65rem;
            font-size: 0.775rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0.55rem 1rem;
            width: 100%;
            transition: all var(--transition);
            letter-spacing: 0.03em;
        }

        .logout-btn:hover {
            background: rgba(239,68,68,0.25);
            color: #fff;
            border-color: rgba(239,68,68,0.4);
        }

        /* ── Mobile Sidebar Overlay ─────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(3px);
            z-index: 999;
            opacity: 0;
            transition: opacity var(--transition);
        }

        /* ── Hamburger ──────────────────────────────────────── */
        .hamburger {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            background: var(--primary);
            border: none;
            border-radius: 0.65rem;
            padding: 0.55rem;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: all var(--transition);
        }

        .hamburger svg {
            width: 22px;
            height: 22px;
            color: #fff;
            display: block;
        }

        /* ── Main Content ────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            padding: 2rem 2.25rem;
            min-height: 100vh;
            transition: margin-left var(--transition);
        }

        /* ── Top Bar ─────────────────────────────────────────── */
        .page-title {
            font-size: 1.05rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        /* ── Stat Cards ──────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--surface-card);
            border-radius: var(--radius);
            padding: 1.25rem 1.35rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition), box-shadow var(--transition);
            border-left: 4px solid #3b82f6;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            background: currentColor;
            opacity: 0.03;
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.4rem;
        }

        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .stat-label {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 0.2rem;
        }

        /* ── Cards ───────────────────────────────────────────── */
        .card {
            background: var(--surface-card);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            gap: 1rem;
        }

        .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ── Table ───────────────────────────────────────────── */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .custom-table th {
            background: #f9fafb;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            padding: 0.85rem 1rem;
            border-bottom: 2px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        .custom-table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            vertical-align: middle;
        }

        .custom-table tbody tr:last-child td { border-bottom: none; }

        .custom-table tbody tr {
            transition: background var(--transition);
        }

        .custom-table tbody tr:hover { background: #fafafa; }

        /* ── Badges ──────────────────────────────────────────── */
        .badge {
            padding: 0.25rem 0.8rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-accepted { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-admin    { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
        .badge-petugas  { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }

        .sk-badge {
            background: linear-gradient(135deg, #ede9fe, #dbeafe);
            color: var(--primary);
            padding: 0.25rem 0.8rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: 0.04em;
            display: inline-block;
        }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            letter-spacing: 0.03em;
            position: relative;
            overflow: hidden;
        }

        .btn:disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: white;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .btn:active::after { opacity: 0.15; }

        .btn-primary  { background: var(--primary); color: #fff; box-shadow: 0 2px 8px rgba(53,52,139,0.25); }
        .btn-primary:hover  { background: var(--primary-light); box-shadow: 0 4px 15px rgba(53,52,139,0.35); transform: translateY(-1px); }

        .btn-accent   { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(211,159,40,0.25); }
        .btn-accent:hover   { background: var(--accent-light); }

        .btn-danger   { background: #ef4444; color: #fff; box-shadow: 0 2px 8px rgba(239,68,68,0.25); }
        .btn-danger:hover   { background: #dc2626; transform: translateY(-1px); }

        .btn-success  { background: #22c55e; color: #fff; box-shadow: 0 2px 8px rgba(34,197,94,0.25); }
        .btn-success:hover  { background: #16a34a; transform: translateY(-1px); }

        .btn-cancel   { background: #f3f4f6; color: #4b5563; border: 1px solid var(--border); }
        .btn-cancel:hover   { background: #e5e7eb; }

        .btn-sm { padding: 0.35rem 0.9rem; font-size: 0.72rem; }

        /* Loading spinner for buttons */
        .btn.loading { pointer-events: none; opacity: 0.75; }
        .btn.loading::before {
            content: '';
            width: 12px; height: 12px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Forms ───────────────────────────────────────────── */
        .form-group {
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            font-weight: 600;
        }

        .form-input {
            padding: 0.7rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 0.65rem;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
            font-size: 0.875rem;
            font-family: inherit;
            background: #fff;
            color: var(--text-primary);
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(53,52,139,0.1);
        }

        .form-input::placeholder { color: #9ca3af; }

        .form-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* ── Flash Alerts ────────────────────────────────────── */
        .alert {
            padding: 0.9rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            animation: slideInAlert 0.35s ease forwards;
        }

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            opacity: 0.6;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0 0.25rem;
            transition: opacity 0.2s;
            color: inherit;
        }

        .alert-close:hover { opacity: 1; }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #ecfdf5);
            color: #065f46;
            border-left: 4px solid #22c55e;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2, #fef2f2);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        @keyframes slideInAlert {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOutAlert {
            from { opacity: 1; transform: translateY(0); max-height: 80px; }
            to   { opacity: 0; transform: translateY(-8px); max-height: 0; margin: 0; padding: 0; }
        }

        .alert.fade-out { animation: fadeOutAlert 0.4s ease forwards; }

        /* ── Greeting & Clock ────────────────────────────────── */
        .greeting h1 { font-size: 1.45rem; margin-bottom: 0.2rem; line-height: 1.3; }
        .greeting h1 span { color: var(--primary); }
        .greeting p { font-size: 0.82rem; color: var(--text-muted); }

        .live-clock {
            text-align: right;
            background: var(--surface-card);
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
        }

        .clock-time {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 2px;
        }

        .clock-date { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.1rem; }

        /* ── Activity Feed ───────────────────────────────────── */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child { border-bottom: none; }

        .activity-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
            margin-top: 5px;
            flex-shrink: 0;
        }

        .activity-main { font-size: 0.875rem; line-height: 1.5; }
        .activity-meta { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem; }

        /* ── Empty State ─────────────────────────────────────── */
        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-state-icon { font-size: 2.75rem; margin-bottom: 0.9rem; }
        .empty-state-text { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
        .empty-state-sub { font-size: 0.82rem; color: var(--text-muted); }

        /* ── Modal ───────────────────────────────────────────── */
        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-bg.show { display: flex; animation: fadeIn 0.2s ease; }

        .modal-box {
            background: var(--surface-card);
            padding: 2rem;
            border-radius: 1.25rem;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            animation: scaleIn 0.25s ease;
        }

        @keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* ── Print ───────────────────────────────────────────── */
        @media print {
            .sidebar, .hamburger, .sidebar-overlay, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd; }
            .custom-table th { background: #f0f0f0 !important; }
            body { font-size: 11pt; }
        }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w-mob);
                border-radius: 0 1.25rem 1.25rem 0;
            }

            .sidebar.open { transform: translateX(0); }

            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }

            .hamburger { display: flex; }

            .main-content {
                margin-left: 0;
                padding: 4.5rem 1rem 1.5rem;
            }

            .stats-grid { grid-template-columns: 1fr 1fr; }

            .greeting h1 { font-size: 1.1rem; }
            .clock-time { font-size: 1.1rem; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

@auth

{{-- ── Mobile Hamburger ──────────────────────────────────────────── --}}
<button class="hamburger" id="hamburgerBtn" aria-label="Toggle sidebar">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- ── Sidebar Overlay (mobile) ───────────────────────────────────── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
    <div class="sidebar-header">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('logo-dishub.png') }}" alt="Logo Dinas Perhubungan Kabupaten Gianyar">
        </div>
        <h3>DINAS PERHUBUNGAN<br>KABUPATEN GIANYAR</h3>
    </div>

    <nav class="sidebar-nav" role="navigation">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                <span>DASHBOARD</span>
            </a>
            <a href="{{ route('admin.pemberian-nomor') }}"
               class="sidebar-link {{ request()->routeIs('admin.pemberian-nomor') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>PEMBERIAN NOMOR</span>
            </a>
            <a href="{{ route('admin.riwayat') }}"
               class="sidebar-link {{ request()->routeIs('admin.riwayat') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>RIWAYAT PENOMORAN</span>
            </a>
            <a href="{{ route('admin.manajemen-user') }}"
               class="sidebar-link {{ request()->routeIs('admin.manajemen-user') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>MANAJEMEN USER</span>
            </a>
        @else
            <a href="{{ route('petugas.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                <span>DASHBOARD</span>
            </a>
            <a href="{{ route('petugas.input-data') }}"
               class="sidebar-link {{ request()->routeIs('petugas.input-data') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>INPUT PENGAJUAN</span>
            </a>
            <a href="{{ route('petugas.riwayat') }}"
               class="sidebar-link {{ request()->routeIs('petugas.riwayat') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>RIWAYAT PENGAJUAN</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ strtoupper(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                KELUAR
            </button>
        </form>
    </div>
</aside>

@endauth

{{-- ── Main content area ────────────────────────────────────────────── --}}
<div class="{{ auth()->check() ? 'main-content' : '' }}">

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert" id="flash-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="dismissAlert(this)" aria-label="Tutup">×</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" role="alert" id="flash-error">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="dismissAlert(this)" aria-label="Tutup">×</button>
        </div>
    @endif

    {{ $slot }}
</div>

<script>
    // ── Live Clock ────────────────────────────────────────────────
    (function updateClock() {
        const timeEl = document.getElementById('live-clock-time');
        const dateEl = document.getElementById('live-clock-date');
        if (timeEl) {
            const now = new Date();
            timeEl.textContent = now.toLocaleTimeString('id-ID');
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });
            }
        }
        setTimeout(updateClock, 1000);
    })();

    // ── Flash Alert Auto-dismiss (5 seconds) ──────────────────────
    function dismissAlert(btn) {
        const alert = btn.closest('.alert');
        if (!alert) return;
        alert.classList.add('fade-out');
        alert.addEventListener('animationend', () => alert.remove(), { once: true });
    }

    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            if (document.body.contains(alert)) {
                alert.classList.add('fade-out');
                alert.addEventListener('animationend', () => alert.remove(), { once: true });
            }
        }, 5000);
    });

    // ── Mobile Sidebar Toggle ─────────────────────────────────────
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const hambBtn  = document.getElementById('hamburgerBtn');

    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    hambBtn?.addEventListener('click', function() {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar on nav link click (mobile only)
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    // ── Form Submit Loading State ─────────────────────────────────
    document.querySelectorAll('form[data-loading]').forEach(function(form) {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                const originalText = btn.textContent;
                btn.classList.add('loading');
                btn.disabled = true;
                btn.dataset.originalText = originalText;
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
