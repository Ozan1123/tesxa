@extends('layouts.admin')

@section('title', 'Laporan Kunjungan')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h2 style="font-size: 1rem; font-weight: 600;">Arsip Kunjungan</h2>
            <div style="display: flex; gap: 1rem;">
                <!-- Placeholder Export Button -->
                <button onclick="window.print()" class="btn btn-secondary">Cetak Laporan (PDF)</button>
            </div>
        </div>

        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tamu</th>
                    <th>Instansi / Tipe</th>
                    <th>Tujuan</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $visit)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($visit->check_in_time)->format('d M Y') }}</td>
                        <td>{{ $visit->guest->name }}</td>
                        <td>{{ $visit->guest->institution ?? $visit->guest->guest_type }}</td>
                        <td>{{ $visit->purpose }}</td>
                        <td>{{ \Carbon\Carbon::parse($visit->check_in_time)->format('H:i') }}</td>
                        <td>
                            @if($visit->check_out_time)
                                {{ \Carbon\Carbon::parse($visit->check_out_time)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding: 1rem;">
            {{ $visits->links() }}
        </div>
    </div>
@endsection