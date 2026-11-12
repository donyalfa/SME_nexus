<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white">Dashboard Modul</h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Pilih modul untuk mengelola bisnis Anda</p>
            </div>
            <a href="{{ route('intelligence.autopsy') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#4B4FE0] px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#4B4FE0]/30 transition hover:bg-[#5B5FF0]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Buka Autopsy
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Module Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <!-- Intelligence Module -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-1 hover:shadow-xl dark:bg-[#14173D] dark:ring-white/10">
                    <div class="absolute right-0 top-0 h-32 w-32 translate-x-8 -translate-y-8 rounded-full bg-gradient-to-br from-[#4B4FE0]/20 to-[#6A5CFF]/20 blur-2xl transition group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-[#4B4FE0] to-[#6A5CFF] text-white shadow-lg shadow-[#4B4FE0]/30">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Intelligence</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Analisis data bisnis dan prediksi tren pasar untuk pengambilan keputusan yang lebih baik.
                        </p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4B4FE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Analisis Tren Pasar
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4B4FE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Prediksi Penjualan
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4B4FE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Laporan Otomatis
                            </li>
                        </ul>
                        <a href="#" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[#4B4FE0] transition hover:gap-3">
                            Buka Modul
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Collaboration Module -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-1 hover:shadow-xl dark:bg-[#14173D] dark:ring-white/10">
                    <div class="absolute right-0 top-0 h-32 w-32 translate-x-8 -translate-y-8 rounded-full bg-gradient-to-br from-[#4FE3C1]/20 to-[#0B7A62]/20 blur-2xl transition group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-[#4FE3C1] to-[#0B7A62] text-white shadow-lg shadow-[#4FE3C1]/30">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Collaboration</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Kolaborasi tim dan manajemen proyek untuk meningkatkan produktivitas bisnis.
                        </p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4FE3C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Manajemen Tim
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4FE3C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Tracking Proyek
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#4FE3C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Komunikasi Real-time
                            </li>
                        </ul>
                        <a href="#" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[#4FE3C1] transition hover:gap-3">
                            Buka Modul
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Intelligence Module -->
                <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-1 hover:shadow-xl dark:bg-[#14173D] dark:ring-white/10">
                    <div class="absolute right-0 top-0 h-32 w-32 translate-x-8 -translate-y-8 rounded-full bg-gradient-to-br from-[#FFB36B]/20 to-[#FF7A90]/20 blur-2xl transition group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-[#FFB36B] to-[#FF7A90] text-white shadow-lg shadow-[#FFB36B]/30">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Intelligence</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Pengambilan keputusan berbasis data dan analisis bisnis yang komprehensif.
                        </p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#FFB36B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Intelligence
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#FFB36B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Analisis Kompetitor
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#FFB36B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Strategi Bisnis
                            </li>
                        </ul>
                        <a href="{{ route('intelligence.autopsy') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[#FFB36B] transition hover:gap-3">
                            Buka Modul
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Quick Stats -->
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-[#14173D] dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-[#4B4FE0]/10 text-[#4B4FE0]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Modul</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">3 Modul</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-[#14173D] dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-[#4FE3C1]/10 text-[#4FE3C1]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Modul Aktif</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">1 Modul</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-[#14173D] dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-[#FFB36B]/10 text-[#FFB36B]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Update Terakhir</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
