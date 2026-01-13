<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Devacto FaceID</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/core.css') }}">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--color-gray-50);
        }

        .sidebar {
            width: 260px;
            background: var(--brand-primary);
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
        }

        .sidebar-header {
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            font-weight: 700;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: var(--brand-accent);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .nav-menu {
            padding: 1.5rem 1rem;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: var(--color-gray-400);
            text-decoration: none;
            border-radius: var(--radius-md);
            margin-bottom: 4px;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item svg {
            width: 20px;
            height: 20px;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.75rem;
            color: var(--color-gray-500);
        }

        .main-wrapper {
            flex: 1;
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        .topbar {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .page-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .content {
            padding: 2rem;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 70px;
            }

            .nav-item span,
            .logo span,
            .sidebar-footer {
                display: none;
            }

            .nav-item {
                justify-content: center;
                padding: 1rem 0;
            }

            .main-wrapper {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">DF</div>
                <span>Devacto Admin</span>
            </div>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('admin.index') }}"
                class="nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.monitoring') }}"
                class="nav-item {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('admin.vip') }}" class="nav-item {{ request()->routeIs('admin.vip') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Tamu Terdaftar</span>
            </a>
            <a href="{{ route('admin.reports') }}"
                class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>Laporan</span>
            </a>
            <a href="/" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 10l5 5-5 5" />
                    <path d="M4 4v7a4 4 0 0 0 4 4h12" />
                </svg>
                <span>Live Scanner</span>
            </a>
        </nav>
        <div class="sidebar-footer">&copy; 2026 Devacto FaceID</div>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <h1 class="page-title">@yield('title')</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 0.875rem; color: var(--text-muted);">{{ date('l, d F Y') }}</span>
                <div
                    style="width: 32px; height: 32px; background: var(--brand-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                    A</div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div
                    style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>