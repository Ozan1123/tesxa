@extends('layouts.admin')

@section('title', 'Monitoring Piket')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h2 style="font-size: 1rem; font-weight: 600;">Tamu Berkunjung (Active)</h2>
            <a href="{{ route('admin.monitoring') }}" class="btn btn-secondary" style="font-size: 0.875rem;">Refresh
                Data</a>
        </div>

        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Jam Masuk</th>
                    <th>Tamu</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeVisits as $visit)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($visit->check_in_at)->format('H:i') }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ asset('storage/' . $visit->guest->photo_path) }}"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 500;">{{ $visit->guest->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $visit->guest->institution ?? $visit->guest->guest_type }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $visit->purpose }}</td>
                        <td><span
                                style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 99px; font-size: 0.75rem; font-weight: 600;">Sedang
                                Berkunjung</span></td>
                        <td>
                            <form action="{{ route('visits.forceCheckout', $visit->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger"
                                    style="padding: 4px 12px; font-size: 0.75rem;">Force Checkout</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada tamu yang
                            sedang berkunjung.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
