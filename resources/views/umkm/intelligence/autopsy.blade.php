<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white">UMKM Intelligence</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Cari tahu kenapa omzet, customer, atau penjualan turun</p>
        </div>
    </x-slot>

    <style>
        .ax { --ink:#171A44; --muted:#666B92; --line:#DADDF4; --card:#fff; --field:#F6F7FF; --brand:#4B4FE0;
              max-width:80rem; margin:0 auto; padding:2rem 1rem 3rem; color:var(--ink); box-sizing:border-box; }
        .ax *, .ax *::before, .ax *::after { box-sizing:border-box; }
        [data-bs-theme="dark"] .ax, .dark .ax { --ink:#F1F2FF; --muted:#A3A8D6; --line:#2B3070; --card:#1B1F4F; --field:#232868; --brand:#8C93FF; }
        .ax svg { display:block; flex:none; }
        .ax p, .ax h1, .ax dl, .ax dd, .ax dt, .ax legend { margin:0; padding:0; }
        .ax fieldset { min-width:0; border:1px solid var(--line); }
        .ax-alert { border:1px solid #F7C7CB; background:#FDECEE; color:#8E1F25; border-radius:1rem; padding:1rem 1.2rem; margin-bottom:1.5rem; font-size:.9rem; line-height:1.5; }
        .ax-alert strong { display:block; margin-bottom:.35rem; }
        .ax-alert ul { margin:0; padding-left:1.2rem; }

        .ax-grid { display:grid; gap:1.5rem; grid-template-columns:minmax(0,1fr); }
        @media (min-width:1024px) { .ax-grid { grid-template-columns:minmax(0,2fr) minmax(0,3fr); align-items:start; } .ax-monitor { position:sticky; top:1.5rem; } }

        /* Monitor */
        .ax-monitor { background:#171A44; color:#fff; border-radius:1.5rem; padding:1.5rem; position:relative; overflow:hidden; box-shadow:0 30px 60px -30px rgba(23,26,68,.6); animation:axRise .8s cubic-bezier(.22,1,.36,1) both; }
        .ax-monitor::before { content:""; position:absolute; right:-70px; top:-70px; width:220px; height:220px; border-radius:50%; background:rgba(106,92,255,.4); filter:blur(48px); pointer-events:none; }
        .ax-monitor > * { position:relative; }
        .ax-top { display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; }
        .ax-title { font-size:1.4rem; font-weight:700; line-height:1.25; color:#fff; }
        .ax-sub { margin-top:.25rem !important; font-size:.85rem; color:rgba(255,255,255,.6); }
        .ax-pill { display:flex; align-items:center; gap:.5rem; flex:none; padding:.35rem .8rem; border-radius:999px; background:rgba(255,255,255,.1); box-shadow:inset 0 0 0 1px rgba(255,255,255,.15); font-size:.75rem; font-weight:600; white-space:nowrap; transition:color .5s; }
        .ax-dot { width:8px; height:8px; border-radius:50%; background:currentColor; animation:axPing var(--spd,2.4s) ease-out infinite; }
        .ax-ecg { margin-top:1.25rem; height:64px; transition:color .5s; }
        .ax-ecg svg { width:100%; height:100%; }
        .ax-ecg-base { stroke:rgba(255,255,255,.12); }
        .ax-ecg-beat { stroke-dasharray:22 78; animation:axBeat var(--spd,2.4s) linear infinite; filter:drop-shadow(0 0 6px currentColor); }
        .ax-meters { margin-top:1.25rem; display:grid; gap:1rem; }
        .ax-mrow { display:flex; justify-content:space-between; align-items:baseline; font-size:.85rem; color:rgba(255,255,255,.7); }
        .ax-mval { font-size:1.15rem; font-weight:700; font-variant-numeric:tabular-nums; }
        .ax-track { position:relative; height:8px; margin-top:.4rem; border-radius:99px; background:rgba(255,255,255,.1); }
        .ax-track i { position:absolute; left:50%; top:-3px; width:1px; height:14px; background:rgba(255,255,255,.3); }
        .ax-track b { position:absolute; top:0; height:8px; border-radius:99px; }
        .ax-derived { margin-top:1.5rem; display:grid; grid-template-columns:1fr 1fr; gap:1px; background:rgba(255,255,255,.1); border-radius:1rem; overflow:hidden; box-shadow:0 0 0 1px rgba(255,255,255,.1); }
        .ax-derived div { background:#171A44; padding:.85rem; }
        .ax-derived dt { font-size:.72rem; color:rgba(255,255,255,.55); }
        .ax-derived dd { margin-top:.15rem !important; font-size:.9rem; font-weight:600; font-variant-numeric:tabular-nums; }
        .ax-hint-box { margin-top:1.25rem !important; padding:.9rem; border-radius:.9rem; background:rgba(255,255,255,.07); box-shadow:inset 0 0 0 1px rgba(255,255,255,.1); font-size:.85rem; line-height:1.55; color:rgba(255,255,255,.88); animation:axHint .35s ease both; }

        /* Form */
        .ax-form { display:grid; gap:1.1rem; min-width:0; }
        .ax .ax-group { background:var(--card); border-radius:1.1rem; padding:1.5rem; margin:0; box-shadow:0 1px 2px rgba(23,26,68,.06); opacity:0; animation:axSlide .6s cubic-bezier(.22,1,.36,1) forwards; animation-delay:var(--d,0s); transition:border-color .3s, box-shadow .3s; }
        .ax-group:focus-within { border-color:var(--brand); box-shadow:0 18px 40px -24px rgba(75,79,224,.55); }
        .ax-head { display:flex; align-items:center; gap:.85rem; margin-bottom:1.25rem; }
        .ax-chip { width:36px; height:36px; border-radius:11px; display:grid; place-items:center; transition:transform .3s cubic-bezier(.34,1.56,.64,1); }
        .ax-chip svg { width:20px; height:20px; }
        .ax-group:focus-within .ax-chip { transform:rotate(-8deg) scale(1.1); }
        .ax-gtitle { display:block; font-size:1rem; font-weight:700; line-height:1.2; color:var(--ink); }
        .ax-gdesc { font-size:.78rem; color:var(--muted); margin-top:2px !important; }
        .ax-row { display:grid; gap:1.1rem; grid-template-columns:minmax(0,1fr); }
        @media (min-width:640px) { .ax-row { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        .ax-label { display:block; margin-bottom:.4rem; font-size:.85rem; font-weight:500; color:var(--ink); }
        .ax-wrap { position:relative; }
        .ax-pre { position:absolute; left:.9rem; top:0; bottom:0; display:grid; place-items:center; font-size:.82rem; font-weight:600; color:var(--muted); pointer-events:none; }
        .ax-field { width:100%; height:44px; padding:0 1rem; border:1px solid var(--line); border-radius:.8rem; background:var(--field); color:var(--ink); font-size:.95rem; font-family:inherit; transition:border-color .2s, box-shadow .2s, background .2s; -moz-appearance:textfield; }
        .ax-field.has-pre { padding-left:2.6rem; }
        .ax-field::-webkit-outer-spin-button, .ax-field::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
        .ax-field::placeholder { color:var(--muted); opacity:.5; }
        .ax-field:focus { outline:none; border-color:var(--brand); background:var(--card); box-shadow:0 0 0 4px rgba(75,79,224,.15); }
        .ax-fmt { height:1rem; margin-top:.3rem !important; font-size:.75rem; color:var(--muted); animation:axHint .35s ease both; }

        .ax-submit { display:flex; flex-direction:column; gap:1rem; }
        @media (min-width:640px) { .ax-submit { flex-direction:row; align-items:center; justify-content:space-between; } }
        .ax-prog { flex:1; }
        .ax-prog-top { display:flex; justify-content:space-between; font-size:.75rem; font-weight:500; color:var(--muted); }
        .ax-bar { height:8px; margin-top:.45rem; border-radius:99px; background:var(--line); overflow:hidden; }
        .ax-bar span { display:block; height:100%; border-radius:99px; background:linear-gradient(90deg,#4B4FE0,#4FE3C1); transition:width .5s ease; }
        .ax-btn { position:relative; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; height:46px; padding:0 1.75rem; border:0; border-radius:.8rem; background:linear-gradient(90deg,#4B4FE0,#6A5CFF); color:#fff; font-size:.9rem; font-weight:700; font-family:inherit; cursor:pointer; transition:transform .25s, box-shadow .25s, opacity .25s; }
        .ax-btn:disabled { opacity:.45; cursor:not-allowed; }
        .ax-btn.is-ready:not(:disabled) { box-shadow:0 12px 24px -10px rgba(75,79,224,.7); }
        .ax-btn.is-ready:not(:disabled):hover { transform:translateY(-2px); }
        .ax-btn:active:not(:disabled) { transform:scale(.97); }
        .ax-btn:focus-visible { outline:2px solid var(--brand); outline-offset:3px; }
        .ax-btn::after { content:""; position:absolute; inset:0; background:linear-gradient(105deg,transparent 35%,rgba(255,255,255,.35) 50%,transparent 65%); transform:translateX(-120%); }
        .ax-btn.is-ready::after { animation:axShine 2.8s ease-in-out infinite; }
        .ax-btn svg { width:16px; height:16px; }
        .ax-spin { animation:axSpin .8s linear infinite; }

        @keyframes axBeat { from { stroke-dashoffset:100; } to { stroke-dashoffset:0; } }
        @keyframes axPing { 0% { box-shadow:0 0 0 0 currentColor; } 70%,100% { box-shadow:0 0 0 9px transparent; } }
        @keyframes axRise { from { opacity:0; transform:translateY(24px) scale(.97); } to { opacity:1; transform:none; } }
        @keyframes axSlide { from { opacity:0; transform:translateX(22px); } to { opacity:1; transform:none; } }
        @keyframes axHint { from { opacity:0; transform:translateY(-3px); } to { opacity:1; transform:none; } }
        @keyframes axShine { 0%,55% { transform:translateX(-120%); } 100% { transform:translateX(120%); } }
        @keyframes axSpin { to { transform:rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) {
            .ax-monitor, .ax-group, .ax-hint-box, .ax-fmt, .ax-ecg-beat, .ax-dot, .ax-btn::after, .ax-spin { animation:none !important; opacity:1 !important; transform:none !important; }
        }
    </style>

    @php
        $groups = [
            ['Omzet', 'Total pemasukan sebelum dikurangi biaya', '#4B4FE0', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', [
                ['omzet_prev', 'Periode sebelumnya', '50000000', 'Rp'],
                ['omzet_current', 'Periode sekarang', '40000000', 'Rp'],
            ]],
            ['Customer', 'Jumlah pembeli unik', '#16B89A', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', [
                ['customer_prev', 'Periode sebelumnya', '500', null],
                ['customer_current', 'Periode sekarang', '400', null],
            ]],
            ['Penjualan', 'Jumlah transaksi atau unit terjual', '#F59A3C', 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', [
                ['sales_prev', 'Periode sebelumnya', '1000', null],
                ['sales_current', 'Periode sekarang', '800', null],
            ]],
            ['Produk', 'Katalog dan harga jual', '#6A5CFF', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', [
                ['product_count', 'Jumlah produk', '15', null],
                ['avg_price', 'Harga rata-rata', '50000', 'Rp'],
            ]],
            ['Biaya', 'Pengeluaran periode sekarang', '#F0506A', 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', [
                ['marketing_spend', 'Biaya marketing', '5000000', 'Rp'],
                ['operational_cost', 'Biaya operasional', '30000000', 'Rp'],
            ]],
        ];
        $ecg = 'M0 32 H60 L68 32 L74 14 L82 52 L90 6 L98 32 H140 L148 32 L154 22 L160 32 H210 L218 32 L224 14 L232 52 L240 6 L248 32 H300';
        $metricFields = [
            'omzet_prev', 'omzet_current', 'customer_prev', 'customer_current',
            'sales_prev', 'sales_current', 'product_count', 'avg_price',
            'marketing_spend', 'operational_cost',
        ];
        $initialMetrics = [];
        foreach ($metricFields as $metric) {
            $initialMetrics[$metric] = old($metric, '');
        }
    @endphp

    <div class="ax" x-data="autopsy({{ \Illuminate\Support\Js::from($initialMetrics) }})">
        @if ($errors->any())
            <div class="ax-alert" role="alert">
                <strong>Data belum dapat diproses.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="ax-grid">

            <!-- Monitor langsung -->
            <aside class="ax-monitor">
                <div class="ax-top">
                    <div>
                        <h1 class="ax-title">Denyut bisnis Anda</h1>
                        <p class="ax-sub">Angka di bawah berubah saat Anda mengisi form.</p>
                    </div>
                    <div class="ax-pill" :style="`color:${status.color};--spd:${status.speed}`">
                        <span class="ax-dot"></span>
                        <span x-text="status.label">Menunggu data</span>
                    </div>
                </div>

                <div class="ax-ecg" :style="`color:${status.color};--spd:${status.speed}`">
                    <svg viewBox="0 0 300 60" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" preserveAspectRatio="none" aria-hidden="true">
                        <path class="ax-ecg-base" pathLength="100" d="{{ $ecg }}"/>
                        <path class="ax-ecg-beat" stroke="currentColor" pathLength="100" d="{{ $ecg }}"/>
                    </svg>
                </div>

                <div class="ax-meters">
                    <template x-for="m in meters" :key="m.key">
                        <div>
                            <div class="ax-mrow">
                                <span x-text="m.label"></span>
                                <span class="ax-mval" :style="`color:${tone(ch[m.key])}`" x-text="ch[m.key] === null ? '—' : fmtPct(shown[m.key])">—</span>
                            </div>
                            <div class="ax-track"><i></i><b :style="bar(m.key)"></b></div>
                        </div>
                    </template>
                </div>

                <dl class="ax-derived">
                    <template x-for="d in derived" :key="d.label">
                        <div>
                            <dt x-text="d.label"></dt>
                            <dd x-text="d.value"></dd>
                        </div>
                    </template>
                </dl>

                <template x-if="true">
                    <p class="ax-hint-box" :key="hint" x-text="hint"></p>
                </template>
            </aside>

            <!-- Form -->
            <form method="POST" action="{{ route('intelligence.analyze') }}" class="ax-form" @submit="loading = true">
                @csrf

                <!-- Periode (opsional) -->
                <fieldset class="ax-group" style="--d: 0.06s; border:1px solid var(--line)">
                    <div class="ax-head">
                        <span class="ax-chip" style="background: #8C93FF22; color: #8C93FF">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <div>
                            <legend class="ax-gtitle">Periode</legend>
                            <p class="ax-gdesc">Opsional — menandai rentang waktu data ini di riwayat analisis</p>
                        </div>
                    </div>

                    <div class="ax-row">
                        <div>
                            <label for="period_prev_date" class="ax-label">Periode lama</label>
                            <input id="period_prev_date" type="date" name="period_prev_date" value="{{ old('period_prev_date') }}" max="{{ now()->toDateString() }}" class="ax-field">
                        </div>
                        <div>
                            <label for="period_current_date" class="ax-label">Periode sekarang</label>
                            <input id="period_current_date" type="date" name="period_current_date" value="{{ old('period_current_date') }}" max="{{ now()->toDateString() }}" class="ax-field">
                        </div>
                    </div>
                </fieldset>

                @foreach ($groups as $i => [$title, $desc, $color, $icon, $fields])
                    <fieldset class="ax-group" style="--d: {{ 0.15 + $i * 0.09 }}s">
                        <div class="ax-head">
                            <span class="ax-chip" style="background: {{ $color }}22; color: {{ $color }}">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            </span>
                            <div>
                                <legend class="ax-gtitle">{{ $title }}</legend>
                                <p class="ax-gdesc">{{ $desc }}</p>
                            </div>
                        </div>

                        <div class="ax-row">
                            @foreach ($fields as [$name, $label, $ph, $prefix])
                                <div>
                                    <label for="{{ $name }}" class="ax-label">{{ $label }}</label>
                                    <div class="ax-wrap">
                                        @if ($prefix)<span class="ax-pre">{{ $prefix }}</span>@endif
                                        <input id="{{ $name }}" type="number" inputmode="numeric" min="{{ str_contains($name, '_prev') ? 1 : 0 }}" name="{{ $name }}" x-model="f.{{ $name }}" required placeholder="{{ $ph }}" class="ax-field {{ $prefix ? 'has-pre' : '' }}">
                                    </div>
                                    @if ($prefix)
                                        <p class="ax-fmt" x-show="f.{{ $name }} !== ''" x-text="rp(f.{{ $name }})"></p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                <div class="ax-group ax-submit" style="--d: 0.65s; border:1px solid var(--line)">
                    <div class="ax-prog">
                        <div class="ax-prog-top">
                            <span>Kelengkapan data</span>
                            <span x-text="filled + ' dari 10 kolom'">0 dari 10 kolom</span>
                        </div>
                        <div class="ax-bar"><span :style="`width:${filled * 10}%`" style="width:0"></span></div>
                    </div>

                    <button type="submit" class="ax-btn" :class="{ 'is-ready': filled === 10 }" :disabled="filled < 10 || loading">
                        <svg x-show="loading" x-cloak class="ax-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        <span x-text="loading ? 'Menganalisis…' : 'Mulai analisis'">Mulai analisis</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function autopsy(initial = {}) {
            const defaults = { omzet_prev: '', omzet_current: '', customer_prev: '', customer_current: '', sales_prev: '', sales_current: '', product_count: '', avg_price: '', marketing_spend: '', operational_cost: '' };

            return {
                f: { ...defaults, ...initial },
                shown: { omzet: 0, customer: 0, sales: 0 },
                loading: false,
                meters: [
                    { key: 'omzet', label: 'Omzet' },
                    { key: 'customer', label: 'Customer' },
                    { key: 'sales', label: 'Penjualan' },
                ],
                init() {
                    this.$watch('f', () => this.sync(), { deep: true });
                    window.addEventListener('pageshow', () => { this.loading = false; });
                },
                n(k) { return parseFloat(this.f[k]) || 0; },
                pct(a, b) {
                    const p = this.n(a);
                    return p > 0 && this.f[b] !== '' ? ((this.n(b) - p) / p) * 100 : null;
                },
                get ch() {
                    return {
                        omzet: this.pct('omzet_prev', 'omzet_current'),
                        customer: this.pct('customer_prev', 'customer_current'),
                        sales: this.pct('sales_prev', 'sales_current'),
                    };
                },
                get filled() { return Object.values(this.f).filter(v => v !== '').length; },
                get status() {
                    const { omzet: o, customer: c, sales: s } = this.ch;
                    if (o === null) return { label: 'Menunggu data', color: '#A3A8D6', speed: '3.4s' };

                    const tierOf = v => v === null ? 0 : v > -1 ? 0 : v >= -5 ? 1 : v >= -10 ? 2 : v >= -25 ? 3 : 4;
                    const tiers = [tierOf(o), tierOf(c), tierOf(s)].filter(t => t > 0);
                    const worst = tiers.length ? Math.max(...tiers) : 0;
                    const escalated = tiers.length === 3 ? Math.min(worst + 1, 4) : worst;

                    if (escalated === 4) return { label: 'Perlu Perhatian', color: '#FF7A90', speed: '0.85s' };
                    if (escalated === 3) return { label: 'Perlu Perhatian', color: '#FF7A90', speed: '1.2s' };
                    if (escalated === 2) return { label: 'Perlu Evaluasi', color: '#FFB36B', speed: '1.5s' };
                    if (escalated === 1) return { label: 'Perlu Dipantau', color: '#F5C518', speed: '2s' };
                    return { label: 'Stabil', color: '#4FE3C1', speed: '2.4s' };
                },
                get hint() {
                    const { omzet: o, customer: c, sales: s } = this.ch;
                    if (o === null) return 'Isi omzet dua periode dulu untuk melihat kondisi bisnis Anda.';
                    if (c !== null && s !== null) {
                        if (o <= -1 && c <= -1 && s <= -1) return 'Semua indikator turun bersamaan. Ini tanda masalah yang lebih luas, bukan sekadar fluktuasi.';
                        if (c < s && c <= -10) return 'Customer turun lebih tajam dari penjualan. Kemungkinan masalah ada di jumlah pembeli.';
                        if (s <= -10 && c > -5) return 'Customer relatif stabil tapi penjualan turun. Pembeli belanja lebih sedikit.';
                    }
                    if (o > -1) return 'Omzet belum turun. Lanjutkan, kami tetap cek risiko di biaya dan produk.';
                    if (o >= -5) return 'Omzet turun ringan. Pantau tren beberapa hari ke depan.';
                    if (o >= -10) return 'Omzet turun sedang. Evaluasi kanal penjualan dan strategi harga.';
                    return 'Omzet turun lebih dari 10%. Segera evaluasi penyebab dan tindakan perbaikan.';
                },
                get derived() {
                    const oc = this.n('omzet_current'), sc = this.n('sales_current'), cc = this.n('customer_current');
                    return [
                        { label: 'Rata-rata per transaksi', value: sc > 0 && oc > 0 ? this.rp(oc / sc) : '—' },
                        { label: 'Omzet per customer', value: cc > 0 && oc > 0 ? this.rp(oc / cc) : '—' },
                        { label: 'Marketing / omzet', value: oc > 0 && this.f.marketing_spend !== '' ? (this.n('marketing_spend') / oc * 100).toFixed(1) + '%' : '—' },
                        { label: 'Operasional / omzet', value: oc > 0 && this.f.operational_cost !== '' ? (this.n('operational_cost') / oc * 100).toFixed(1) + '%' : '—' },
                    ];
                },
                rp(v) {
                    const x = Math.round(parseFloat(v));
                    return isNaN(x) ? '' : 'Rp ' + x.toLocaleString('id-ID');
                },
                fmtPct(v) { return (v > 0 ? '+' : '') + v.toFixed(1) + '%'; },
                tone(v) { return v === null ? '#A3A8D6' : v > -1 ? '#4FE3C1' : v >= -5 ? '#F5C518' : v >= -10 ? '#FFB36B' : '#FF7A90'; },
                bar(k) {
                    const v = this.shown[k] || 0;
                    const w = Math.min(Math.abs(v) / 100 * 50, 50);
                    return `width:${w}%;${v >= 0 ? 'left:50%' : 'right:50%'};background:${this.tone(this.ch[k])}`;
                },
                sync() {
                    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    for (const k of Object.keys(this.shown)) {
                        const to = this.ch[k] ?? 0;
                        if (reduce) { this.shown[k] = to; continue; }
                        const from = this.shown[k], t0 = performance.now();
                        const step = now => {
                            const p = Math.min((now - t0) / 650, 1);
                            this.shown[k] = from + (to - from) * (1 - Math.pow(1 - p, 3));
                            if (p < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    }
                },
            };
        }
    </script>
</x-app-layout>