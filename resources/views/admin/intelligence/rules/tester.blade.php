@extends('admin.layouts.intelligence')

@section('title', 'Rule Tester')
@section('header', 'Rule Tester')
@section('subheader', 'Uji aturan diagnosis secara sementara tanpa menyimpan analisis')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Rule Tester</h1>
            <p class="ba-muted mb-0 small">Masukkan nilai metrik untuk melihat gejala, rule yang cocok, dan diagnosis utama.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.index') }}">Kembali ke Rules</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="ba-card ba-card-body ba-form-summary">
                <h2 class="h6 fw-bold mb-1">Metrik Uji</h2>
                <p class="small ba-muted mb-3">Nilai kosong dianggap tidak tersedia dan tidak memicu gejala.</p>

                @if ($metricInputs === [])
                    <div class="ba-empty">Belum ada gejala aktif. Tambahkan gejala terlebih dahulu.</div>
                @else
                    <form method="POST" action="{{ route('admin.intelligence.rules.tester.run') }}">
                        @csrf

                        <div class="row g-3">
                            @foreach ($metricInputs as $input)
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="metric-{{ $input['key'] }}">{{ $input['key'] }}</label>
                                    <input
                                        class="form-control @error('metrics.'.$input['key']) is-invalid @enderror"
                                        id="metric-{{ $input['key'] }}"
                                        type="number"
                                        step="any"
                                        name="metrics[{{ $input['key'] }}]"
                                        value="{{ $metrics[$input['key']] ?? '' }}"
                                        placeholder="0"
                                    >
                                    <div class="form-text">{{ $input['label'] }}</div>
                                    @foreach ($input['hints'] as $hint)
                                        <div class="form-text"><code>{{ $hint }}</code></div>
                                    @endforeach
                                    @error('metrics.'.$input['key'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button class="btn ba-btn-primary text-white flex-fill" type="submit">Jalankan Uji</button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.tester') }}">Reset</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-7">
            @if ($result)
                <div class="ba-card mb-3">
                    <div class="ba-card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-1">Hasil Uji</h2>
                                <div class="small ba-muted">Tidak disimpan sebagai analisis. Jalankan ulang setelah mengubah metrik.</div>
                            </div>
                            <span class="badge text-bg-light border">{{ $result->matchedCandidates ? count($result->matchedCandidates).' rule cocok' : 'Tidak ada rule cocok' }}</span>
                        </div>

                        <div class="mb-3">
                            <div class="small fw-semibold ba-muted mb-1">Gejala terdeteksi</div>
                            @forelse ($result->detectedSymptoms as $symptom)
                                <span class="badge text-bg-light border me-1 mb-1">{{ $symptom->name }}</span>
                            @empty
                                <span class="small ba-muted">Tidak ada gejala yang terdeteksi dari metrik ini.</span>
                            @endforelse
                        </div>

                        @if ($result->primaryCandidate)
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                    <div>
                                        <div class="small ba-muted">Diagnosis utama</div>
                                        <div class="fs-5 fw-bold">{{ $result->primaryDiagnosis()?->name }}</div>
                                    </div>
                                    <span class="ba-badge {{ $result->primaryCandidate['severity']->badgeClass() }}">
                                        {{ $result->primaryCandidate['severity']->label() }}
                                    </span>
                                </div>

                                <dl class="row small mb-0">
                                    <dt class="col-5 col-md-4">Rule</dt>
                                    <dd class="col-7 col-md-8">{{ $result->primaryRule()?->name }} ({{ $result->primaryRule()?->code }})</dd>
                                    <dt class="col-5 col-md-4">Versi</dt>
                                    <dd class="col-7 col-md-8">v{{ $result->primaryVersion()?->version }}</dd>
                                    <dt class="col-5 col-md-4">Prioritas</dt>
                                    <dd class="col-7 col-md-8">{{ $result->primaryCandidate['priority'] }}</dd>
                                    <dt class="col-5 col-md-4">Spesifisitas</dt>
                                    <dd class="col-7 col-md-8">{{ $result->primaryCandidate['specificity'] }} kondisi</dd>
                                </dl>
                            </div>
                        @else
                            <div class="ba-empty">
                                <div class="fw-semibold mb-1">Tidak ada diagnosis yang cocok</div>
                                <div class="small">Coba ubah nilai metrik atau pastikan rule aktif sudah dipublikasikan.</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($result->recommendations->isNotEmpty())
                    <div class="ba-card mb-3">
                        <div class="ba-card-body">
                            <h2 class="h6 fw-bold mb-3">Rekomendasi</h2>
                            @foreach ($result->recommendations as $recommendation)
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="fw-semibold">{{ $recommendation->title }}</div>
                                    @if ($recommendation->description)
                                        <div class="small ba-muted mb-2">{{ $recommendation->description }}</div>
                                    @endif
                                    @if (! empty($recommendation->action_steps))
                                        <ol class="small mb-0 ps-3">
                                            @foreach ($recommendation->action_steps as $step)
                                                <li>{{ $step }}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="ba-card">
                    <div class="ba-card-body">
                        <h2 class="h6 fw-bold mb-3">Rule yang Cocok</h2>
                        <div class="table-responsive">
                            <table class="table ba-table ba-table-mobile align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rule</th>
                                        <th>Diagnosis</th>
                                        <th>Severity</th>
                                        <th>Prioritas</th>
                                        <th>Spesifisitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($result->matchedCandidates as $index => $candidate)
                                        <tr>
                                            <td data-label="#">{{ $index + 1 }}</td>
                                            <td data-label="Rule">
                                                <div class="fw-semibold">{{ $candidate['rule']->name }}</div>
                                                <div class="small ba-muted">{{ $candidate['rule']->code }} &middot; v{{ $candidate['version']->version }}</div>
                                            </td>
                                            <td data-label="Diagnosis">{{ $candidate['diagnosis']->name }}</td>
                                            <td data-label="Severity"><span class="ba-badge {{ $candidate['severity']->badgeClass() }}">{{ $candidate['severity']->label() }}</span></td>
                                            <td data-label="Prioritas">{{ $candidate['priority'] }}</td>
                                            <td data-label="Spesifisitas">{{ $candidate['specificity'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6"><div class="ba-empty">Belum ada rule yang cocok.</div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="ba-card h-100">
                    <div class="ba-card-body d-flex flex-column justify-content-center">
                        <div class="ba-empty">
                            <div class="fw-semibold mb-1">Hasil uji akan muncul di sini</div>
                            <div class="small">Isi metrik di sebelah kiri lalu jalankan uji. Tester menggunakan rule aktif yang sudah dipublikasikan.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
