<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PANEL // DEVACTO FACEID</title>
    <style>
        /* ============================================
           CYBERPUNK ADMIN THEME
        ============================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #0a0a0a;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            min-height: 100vh;
            padding: 20px;
        }

        /* Header */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #00ff00;
            margin-bottom: 30px;
        }

        .admin-title {
            font-size: 28px;
            text-shadow: 0 0 20px #00ff00;
            letter-spacing: 3px;
        }

        .admin-subtitle {
            font-size: 12px;
            color: #008800;
            margin-top: 5px;
        }

        .btn-back {
            background: transparent;
            border: 2px solid #00ff00;
            color: #00ff00;
            padding: 12px 25px;
            font-family: inherit;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn-back:hover {
            background: #00ff00;
            color: #000;
            box-shadow: 0 0 20px #00ff00;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #00ff00;
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: bold;
            text-shadow: 0 0 10px #00ff00;
        }

        .stat-label {
            font-size: 12px;
            color: #008800;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Table Container */
        .table-container {
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid #00ff00;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.2);
            overflow: hidden;
        }

        .table-header {
            background: rgba(0, 255, 0, 0.1);
            padding: 15px 20px;
            border-bottom: 1px solid #00ff00;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 16px;
            letter-spacing: 2px;
        }

        /* Search Box */
        .search-box {
            background: #000;
            border: 1px solid #00ff00;
            color: #00ff00;
            padding: 8px 15px;
            font-family: inherit;
            width: 250px;
        }

        .search-box:focus {
            outline: none;
            box-shadow: 0 0 10px #00ff00;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: rgba(0, 255, 0, 0.1);
            padding: 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid #00ff00;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(0, 255, 0, 0.2);
            vertical-align: middle;
        }

        tr:hover {
            background: rgba(0, 255, 0, 0.05);
        }

        /* Photo Thumbnail */
        .photo-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 2px solid #00ff00;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
        }

        /* Gender Badge */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-male {
            background: rgba(0, 100, 255, 0.2);
            border: 1px solid #0088ff;
            color: #0088ff;
        }

        .badge-female {
            background: rgba(255, 0, 150, 0.2);
            border: 1px solid #ff0099;
            color: #ff0099;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #555;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* Time Format */
        .time-display {
            font-size: 14px;
            color: #00ff00;
        }

        .date-display {
            font-size: 11px;
            color: #006600;
        }

        /* Scrollable Table Body */
        .table-scroll {
            max-height: calc(100vh - 350px);
            overflow-y: auto;
        }

        .table-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: #000;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: #00ff00;
        }

        /* Footer */
        .admin-footer {
            text-align: center;
            padding: 20px;
            color: #333;
            font-size: 12px;
            margin-top: 30px;
        }

        /* Delete Button */
        .btn-delete {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            color: #ff0000;
            padding: 6px 12px;
            font-family: inherit;
            font-size: 11px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #ff0000;
            color: #000;
            box-shadow: 0 0 10px #ff0000;
        }

        /* Flash Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid;
            font-size: 14px;
        }

        .alert-success {
            background: rgba(0, 255, 0, 0.1);
            border-color: #00ff00;
            color: #00ff00;
        }

        .alert-error {
            background: rgba(255, 0, 0, 0.1);
            border-color: #ff0000;
            color: #ff0000;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="admin-header">
        <div>
            <h1 class="admin-title">>> ADMIN_PANEL.EXE</h1>
            <p class="admin-subtitle">GUEST MANAGEMENT SYSTEM // DEVACTO FACEID</p>
        </div>
        <a href="/" class="btn-back">← Back to Scanner</a>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-number">{{ count($guests) }}</div>
            <div class="stat-label">Total Guests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('created_at', '>=', today())->count() }}</div>
            <div class="stat-label">Today's Visitors</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('guest_type', 'Orang Tua')->count() }}</div>
            <div class="stat-label">Parents</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guests->where('guest_type', 'Alumni')->count() }}</div>
            <div class="stat-label">Alumni</div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-header">
            <span class="table-title">>> GUEST_LOG.DB</span>
            <input type="text" class="search-box" id="searchBox" placeholder="Search name..." onkeyup="filterTable()">
        </div>

        @if($guests->count() > 0)
            <div class="table-scroll">
                <table id="guestTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Time</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guests as $index => $guest)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="time-display">{{ $guest->created_at->format('H:i') }}</div>
                                    <div class="date-display">{{ $guest->created_at->format('d M Y') }}</div>
                                </td>
                                <td>
                                    @if($guest->photo_path)
                                        <img src="{{ asset('storage/' . $guest->photo_path) }}" alt="Photo" class="photo-thumb"
                                            onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22><rect fill=%22%23111%22 width=%2260%22 height=%2260%22/><text x=%2230%22 y=%2235%22 text-anchor=%22middle%22 fill=%22%2300ff00%22 font-size=%2212%22>NO IMG</text></svg>'">
                                    @else
                                        <div class="photo-thumb"
                                            style="display:flex; align-items:center; justify-content:center; background:#111;">
                                            <span style="font-size:10px;">N/A</span>
                                        </div>
                                    @endif
                                </td>
                                <td style="font-weight:bold;">{{ strtoupper($guest->name) }}</td>
                                <td>
                                    <span class="badge {{ $guest->gender == 'male' ? 'badge-male' : 'badge-female' }}">
                                        {{ $guest->gender == 'male' ? '♂ MALE' : '♀ FEMALE' }}
                                    </span>
                                </td>
                                <td>{{ $guest->guest_type }}</td>
                                <td>{{ $guest->purpose }}</td>
                                <td>
                                    <form action="{{ route('guests.destroy', $guest->id) }}" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini? Foto juga akan dihapus permanen.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑 DELETE</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <h3>[ NO DATA ]</h3>
                <p>No guests have been registered yet.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        DEVACTO FACEID v1.0 // {{ date('Y') }}
    </footer>

    <script>
        // Simple Table Filter
        function filterTable() {
            const input = document.getElementById('searchBox');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('guestTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[3]; // Name column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</body>

</html>