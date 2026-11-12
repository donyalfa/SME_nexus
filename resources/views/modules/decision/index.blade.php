<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white">Decision</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Ambil keputusan berbasis riwayat analisis bisnis Anda</p>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'ok'       => ['label' => 'Stabil',          'class' => 'bg-success-subtle text-success'],
            'excellent' => ['label' => 'Sangat Baik',     'class' => 'bg-success-subtle text-success'],
            'monitor'  => ['label' => 'Perlu Dipantau',  'class' => 'bg-info-subtle text-info-emphasis'],
            'warning'  => ['label' => 'Perlu Evaluasi',  'class' => 'bg-warning-subtle text-warning-emphasis'],
            'critical' => ['label' => 'Perlu Perhatian', 'class' => 'bg-danger-subtle text-danger-emphasis'],
        ];

        $latestConfig = $stats['latest']
            ? ($statusLabels[$stats['latest']->status] ?? $statusLabels['monitor'])
            : null;

        $planStatusUi = [
            'todo' => [
                'label' => 'To-Do',
                'class' => 'bg-secondary-subtle text-secondary',
                'next' => 'in_progress',
                'button' => 'btn-outline-primary',
                'action' => 'Mulai Kerjakan',
            ],
            'in_progress' => [
                'label' => 'In Progress',
                'class' => 'bg-info-subtle text-info-emphasis',
                'next' => 'done',
                'button' => 'btn-success',
                'action' => 'Tandai Selesai',
            ],
            'done' => [
                'label' => 'Done',
                'class' => 'bg-success-subtle text-success',
                'next' => 'in_progress',
                'button' => 'btn-outline-secondary',
                'action' => 'Buka Kembali',
            ],
        ];

        $planProgress = $actionPlanStats['total'] > 0
            ? (int) round(($actionPlanStats['done'] / $actionPlanStats['total']) * 100)
            : 0;
    @endphp

    <style>
        .dc-card { border-radius: 1rem; border: 1px solid var(--nx-line); background: var(--nx-surface-solid); }
        .dc-table td, .dc-table th { vertical-align: middle; }
        .ap-item { border: 1px solid var(--nx-line); border-radius: .85rem; padding: 1rem; }
        .ap-item + .ap-item { margin-top: .75rem; }
        .ap-progress { height: .55rem; border-radius: 999px; }
        .ap-task-done { text-decoration: line-through; opacity: .65; }
    </style>

    <div class="mx-auto" style="max-width: 80rem;">

        {{-- Ringkasan analisis --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="dc-card p-4 h-100">
                    <p class="small fw-medium text-body-secondary mb-2">Total Analisis</p>
                    <p class="display-6 fw-bold text-body mb-0">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dc-card p-4 h-100">
                    <p class="small fw-medium text-body-secondary mb-2">Perlu Perhatian</p>
                    <p class="display-6 fw-bold mb-0 {{ $stats['needs_attention'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($stats['needs_attention']) }}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dc-card p-4 h-100">
                    <p class="small fw-medium text-body-secondary mb-2">Status Terakhir</p>
                    @if ($latestConfig)
                        <span class="badge rounded-pill fs-6 {{ $latestConfig['class'] }}">{{ $latestConfig['label'] }}</span>
                        <p class="small text-body-secondary mb-0 mt-2">
                            {{ $stats['latest']->created_at->format('d M Y, H:i') }}
                        </p>
                    @else
                        <p class="text-body-secondary mb-0">Belum ada analisis</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Plan --}}
        <section class="dc-card p-4 mb-4" aria-labelledby="action-plan-heading">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <h3 id="action-plan-heading" class="h5 fw-bold mb-1">Action Plan</h3>
                    <p class="small text-body-secondary mb-0">
                        Ubah diagnosis dan rekomendasi Intelligence menjadi tindakan yang memiliki tenggat dan status.
                    </p>
                </div>
                <span class="badge rounded-pill bg-primary-subtle text-primary">Tindak lanjut bisnis</span>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Periksa kembali data yang dikirim:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Statistik Action Plan --}}
            <div class="row g-2 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="rounded-3 border p-3 h-100">
                        <div class="small text-body-secondary">Total</div>
                        <div class="fs-4 fw-bold">{{ number_format($actionPlanStats['total']) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rounded-3 border p-3 h-100">
                        <div class="small text-body-secondary">To-Do</div>
                        <div class="fs-4 fw-bold">{{ number_format($actionPlanStats['todo']) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rounded-3 border p-3 h-100">
                        <div class="small text-body-secondary">In Progress</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($actionPlanStats['in_progress']) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rounded-3 border p-3 h-100">
                        <div class="small text-body-secondary">Done</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($actionPlanStats['done']) }}</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold">Progress penyelesaian</span>
                    <span class="small fw-bold">{{ $planProgress }}%</span>
                </div>
                <div class="progress ap-progress" role="progressbar"
                     aria-label="Progress Action Plan"
                     aria-valuenow="{{ $planProgress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-success" style="width: {{ $planProgress }}%"></div>
                </div>
            </div>

            {{-- Form tambah tindakan; memakai details agar tidak bergantung pada JavaScript Bootstrap --}}
            @if ($stats['latest'])
                @php
                    $hasSteps = collect($stats['latest']->findings ?? [])
                        ->where('type', 'recommendation')
                        ->contains(fn ($f) => $f->steps->isNotEmpty());
                @endphp
                @unless ($hasSteps)
                <details class="border rounded-3 p-3 mb-4">
                    <summary class="fw-semibold text-primary" style="cursor:pointer">
                        + Tambah Action Plan dari analisis terbaru
                    </summary>
                    <form action="{{ route('decision.action-plans.store') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="id_analysis" value="{{ $stats['latest']->id }}">

                        <div class="mb-3">
                            <label for="ap-task" class="form-label small fw-semibold">Nama tindakan <span class="text-danger">*</span></label>
                            <input id="ap-task" type="text" name="task" maxlength="200"
                                   class="form-control" value="{{ old('task') }}"
                                   placeholder="Contoh: Menjalankan program loyalitas pelanggan" required>
                        </div>

                        <div class="mb-3">
                            <label for="ap-finding" class="form-label small fw-semibold">Diagnosis/rekomendasi terkait</label>
                            <select id="ap-finding" name="id_finding" class="form-select">
                                <option value="">Tindakan umum (tanpa finding khusus)</option>
                                @foreach ($actionPlanFindings as $finding)
                                    <option value="{{ $finding->id }}" @selected(old('id_finding') == $finding->id)>
                                        [{{ ucfirst($finding->type) }}] {{ $finding->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ap-description" class="form-label small fw-semibold">Deskripsi / langkah pelaksanaan</label>
                            <textarea id="ap-description" name="description" rows="3" maxlength="5000"
                                      class="form-control" placeholder="Tuliskan langkah yang akan dilakukan...">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="ap-due-date" class="form-label small fw-semibold">Deadline</label>
                            <input id="ap-due-date" type="date" name="due_date" class="form-control"
                                   value="{{ old('due_date') }}">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan Action Plan</button>
                        </div>
                    </form>
                </details>
                @endunless
            @else
                <div class="alert alert-info">
                    Jalankan analisis Intelligence terlebih dahulu sebelum membuat Action Plan yang terhubung ke diagnosis.
                    <a href="{{ route('intelligence.autopsy') }}" class="alert-link">Mulai analisis</a>.
                </div>
            @endif

            {{-- Pilih langkah rekomendasi untuk dijadikan Action Plan --}}
            @if ($stats['latest'])
                @php
                    $recommendationFindings = $actionPlanFindings
                        ->where('type', 'recommendation');
                @endphp

                <div class="border rounded-3 p-3 mb-4">
                    <h4 class="h6 fw-bold mb-1">Langkah Rekomendasi</h4>
                    <p class="small text-body-secondary mb-3">
                        Pilih langkah yang ingin dijalankan. Sistem akan membuat Action Plan secara otomatis.
                    </p>

                    @forelse ($recommendationFindings as $finding)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary mb-2">Rekomendasi</span>
                                    <h5 class="fw-bold fs-6 mb-1">{{ $finding->title }}</h5>
                                    @if ($finding->description)
                                        <p class="small text-body-secondary mb-0">{{ $finding->description }}</p>
                                    @endif
                                </div>
                            </div>

                            @forelse ($finding->steps as $step)
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 border-top py-3">
                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold">
                                            Langkah {{ $step->step_order }}.
                                        </div>
                                        <div>{{ $step->step }}</div>
                                    </div>

                                    <form action="{{ route('decision.action-plans.from-step') }}"
                                          method="POST"
                                          class="d-flex flex-wrap align-items-end gap-2">
                                        @csrf
                                        <input type="hidden" name="id_analysis" value="{{ $stats['latest']->id }}">
                                        <input type="hidden" name="id_finding" value="{{ $finding->id }}">
                                        <input type="hidden" name="id_step" value="{{ $step->id }}">

                                        <div>
                                            <label class="form-label small mb-1"
                                                   for="due-step-{{ $step->id }}">Deadline</label>
                                            <input id="due-step-{{ $step->id }}"
                                                   type="date"
                                                   name="due_date"
                                                   class="form-control form-control-sm">
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            + Tambahkan
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="small text-body-secondary mt-3 mb-0">
                                    Kondisi bisnis Anda sedang baik. Belum ada langkah khusus, tetapi Anda bisa menambahkan tindakan sendiri di bagian atas.
                                </p>
                            @endforelse
                        </div>
                    @empty
                        <div class="alert alert-info mb-0">
                            Belum ada rekomendasi dengan langkah tindakan pada analisis terbaru.
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- Daftar Action Plan --}}
            @forelse ($actionPlans as $plan)
                @php
                    $config = $planStatusUi[$plan->status] ?? $planStatusUi['todo'];
                    $isOverdue = $plan->isOverdue();
                @endphp

                <article class="ap-item">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge rounded-pill {{ $config['class'] }}">{{ $config['label'] }}</span>
                                @if ($isOverdue)
                                    <span class="badge rounded-pill bg-danger-subtle text-danger">Terlambat</span>
                                @endif
                            </div>

                            <h4 class="h6 fw-bold mb-1 {{ $plan->isCompleted() ? 'ap-task-done' : '' }}">
                                {{ $plan->task }}
                            </h4>

                            @if ($plan->description)
                                <p class="small text-body-secondary mb-2">{{ $plan->description }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-3 small text-body-secondary">
                                @if ($plan->finding)
                                    <span>Dasar: {{ $plan->finding->title }}</span>
                                @endif
                                @if ($plan->due_date)
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                        Deadline: {{ $plan->due_date->format('d M Y') }}
                                    </span>
                                @endif
                                <span>Penanggung jawab: {{ $plan->assignee?->full_name ?? 'Belum ditugaskan' }}</span>
                            </div>

                            @if ($plan->completed_at)
                                <p class="small text-success mt-2 mb-0">
                                    Diselesaikan {{ $plan->completed_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>

                        <form action="{{ route('decision.action-plans.status', $plan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $config['next'] }}">
                            <button type="submit" class="btn btn-sm {{ $config['button'] }}">
                                {{ $config['action'] }}
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="text-center border rounded-3 py-5 px-3">
                    <h4 class="h6 fw-bold">Belum ada Action Plan</h4>
                    <p class="small text-body-secondary mb-0">Buat tindakan pertama dari hasil diagnosis atau rekomendasi Intelligence.</p>
                </div>
            @endforelse

            @if ($actionPlans->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $actionPlans->links() }}
                </div>
            @endif
        </section>

        {{-- Riwayat analisis --}}
        <section class="dc-card p-4">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h3 class="h5 fw-bold mb-0">Riwayat Analisis Bisnis</h3>
                <a href="{{ route('intelligence.autopsy') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2">
                    Analisis Baru
                </a>
            </div>

            @if ($analyses->isEmpty())
                <div class="text-center py-5">
                    <p class="text-body-secondary mb-3">Anda belum melakukan analisis. Jalankan analisis pertama untuk mulai mendapatkan rekomendasi keputusan.</p>
                    <a href="{{ route('intelligence.autopsy') }}" class="btn btn-primary">Mulai Analisis</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover dc-table mb-0">
                        <thead>
                            <tr class="small text-body-secondary">
                                <th scope="col">Tanggal</th>
                                <th scope="col">Periode</th>
                                <th scope="col" class="text-center">Omzet</th>
                                <th scope="col" class="text-center">Customer</th>
                                <th scope="col" class="text-center">Penjualan</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyses as $analysis)
                                <tr>
                                    <td class="text-nowrap">{{ $analysis->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-nowrap">{{ $analysis->periodLabel() ?? '—' }}</td>
                                    @php
                                        $changes = [
                                            $analysis->omzet_change,
                                            $analysis->customer_change,
                                            $analysis->sales_change,
                                        ];
                                    @endphp
                                    @foreach ($changes as $change)
                                        <td class="text-center fw-semibold {{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ ($change >= 0 ? '+' : '') . number_format((float) $change, 1, ',', '.') }}%
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ ($statusLabels[$analysis->status] ?? $statusLabels['monitor'])['class'] }}">
                                            {{ ($statusLabels[$analysis->status] ?? $statusLabels['monitor'])['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('intelligence.autopsy.show', $analysis) }}" class="btn btn-outline-secondary btn-sm">Lihat hasil</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $analyses->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
