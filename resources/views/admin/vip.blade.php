@extends('layouts.admin')

@section('title', 'Kelola Tamu Terdaftar')

@section('content')
    <div class="kpi-grid">
        <div style="flex: 1;">
            <div class="table-container" style="padding: 2rem; max-width: 600px;">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Registrasi Tamu Terdaftar Baru</h2>
                <form action="{{ route('guests.storeVip') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nama Lengkap</label>
                        <input type="text" name="name" required
                            style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Instansi / Jabatan</label>
                        <input type="text" name="institution" required
                            style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Foto Wajah</label>
                        <input type="file" name="photo" required accept="image/*" style="width: 100%;">
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Upload foto jelas
                            (Passport style) untuk akurasi AI.</p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan
                        Tamu Terdaftar</button>
                </form>
            </div>
        </div>
    </div>
@endsection