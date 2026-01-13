<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Devacto FaceID</title>

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

        /* Sidebar */
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

        /* Main Content */
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

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .kpi-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-primary);
            line-height: 1;
        }

        .kpi-trend {
            font-size: 0.75rem;
            color: var(--color-success);
            font-weight: 500;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
        }

        .search-box svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text-muted);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem 1.5rem;
            background: var(--color-gray-50);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-light);
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: var(--color-gray-50);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-gray-200);
            object-fit: cover;
        }

        .badge {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-blue {
            background: rgba(59, 130, 246, 0.1);
            color: var(--brand-accent);
        }

        .badge-purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .badge-gray {
            background: var(--color-gray-100);
            color: var(--color-gray-600);
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
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">DF</div>
                <span>Devacto Admin</span>
            </div>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.monitoring') }}" class="nav-item {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('admin.vip') }}" class="nav-item {{ request()->routeIs('admin.vip') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Tamu VIP</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>
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
            <div style="margin-top: auto;"></div>
        </nav>
        <div class="sidebar-footer">
            &copy; 2024 Devacto FaceID
        </div>
    </aside>

    <!-- Main -->
    <div class="main-wrapper">
        <header class="topbar">
            <h1 class="page-title">Overview</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 0.875rem; color: var(--text-muted);">{{ date('l, d F Y') }}</span>
                <div
                    style="width: 32px; height: 32px; background: var(--brand-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                    A</div>
            </div>
        </header>

        <main class="content">

            <!-- Alert -->
            @if(session('success'))
                <div
                    style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KPIs -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <span class="kpi-label">Total Tamu</span>
                    <span class="kpi-value">{{ count($guests) }}</span>
                    <span class="kpi-trend">All Time</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">Hari Ini</span>
                    <span class="kpi-value">{{ $guests->where('created_at', '>=', today())->count() }}</span>
                    <span class="kpi-trend">Check-ins</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">Tamu Umum</span>
                    <span class="kpi-value">{{ $guests->where('guest_type', 'Tamu Umum')->count() }}</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">VIP / Dinas</span>
                    <span
                        class="kpi-value">{{ $guests->whereIn('guest_type', ['Dinas', 'Tamu Khusus'])->count() }}</span>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2 style="font-size: 1rem; font-weight: 600;">Log Kunjungan Terbaru</h2>
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input type="text" id="searchInput" placeholder="Cari nama atau tujuan..."
                            onkeyup="filterTable()">
                    </div>
                </div>

                @if($guests->count() > 0)
                    <div style="overflow-x: auto;">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Identitas Tamu</th>
                                    <th>Gender</th>
                                    <th>Tipe</th>
                                    <th>Tujuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guests as $guest)
                                    <tr>
                                        <td style="color: var(--text-muted); font-variant-numeric: tabular-nums;">
                                            {{ $guest->created_at->format('H:i') }}
                                            <div style="font-size: 0.75rem; color: var(--color-gray-400);">
                                                {{ $guest->created_at->format('d M') }}</div>
                                        </td>
                                        <td>
                                            <div class="user-cell">
                                                @if($guest->photo_path)
                                                    <img src="{{ asset('storage/' . $guest->photo_path) }}" class="avatar">
                                                @else
                                                    <div class="avatar"
                                                        style="display: flex; align-items: center; justify-content: center; color: white;">
                                                        ?</div>
                                                @endif
                                                <span style="font-weight: 500;">{{ $guest->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $guest->gender == 'male' ? 'L' : 'P' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $guest->guest_type == 'Dinas' ? 'badge-purple' : 'badge-gray' }}">
                                                {{ $guest->guest_type }}
                                            </span>
                                        </td>
                                        <td>{{ $guest->purpose }}</td>
                                        <td>
                                            <form action="{{ route('guests.destroy', $guest->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    style="padding: 4px 8px; font-size: 0.75rem;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada data tamu.
                    </div>
                @endif
            </div>

        </main>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('dataTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[1];
                const tdPurpose = tr[i].getElementsByTagName('td')[4];
                if (tdName || tdPurpose) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValuePurpose = tdPurpose.textContent || tdPurpose.innerText;
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || txtValuePurpose.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>
