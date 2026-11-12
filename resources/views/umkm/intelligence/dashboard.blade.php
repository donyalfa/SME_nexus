@extends('umkm.layouts.intelligence')

@section('title', 'Dashboard')
@section('header', 'Dashboard UMKM')
@section('subheader', 'Ringkasan kondisi bisnis dan diagnosis terbaru')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="ba-card ba-card-body ba-stat h-100">
                <span class="ba-stat-label">Analisis terakhir</span>
                <span class="ba-stat-value">Belum ada</span>
                <span class="small ba-muted">Mulai dengan melengkapi data bisnis.</span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="ba-card ba-card-body ba-stat h-100">
                <span class="ba-stat-label">Action plan aktif</span>
                <span class="ba-stat-value">0</span>
                <span class="small ba-muted">Rencana tindak lanjut yang sedang berjalan.</span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="ba-card ba-card-body ba-stat h-100">
                <span class="ba-stat-label">Data periode</span>
                <span class="ba-stat-value">0</span>
                <span class="small ba-muted">Data bulanan yang sudah dicatat.</span>
            </div>
        </div>
    </div>

    <div class="ba-card">
        <div class="ba-card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="ba-page-title h5 mb-1">Mulai Intelligence</h2>
                    <p class="ba-muted mb-0 small">Lengkapi profil usaha dan data bisnis bulanan untuk mendapatkan diagnosis berbasis aturan.</p>
                </div>
                <button type="button" class="btn ba-btn-primary text-white" disabled>Mulai Analisis</button>
            </div>

            <div class="ba-empty">
                <div class="fw-semibold mb-1">Belum ada data bisnis</div>
                <div class="small">Fitur input data dan analisis akan tersedia pada tahap implementasi berikutnya.</div>
            </div>
        </div>
    </div>
@endsection
