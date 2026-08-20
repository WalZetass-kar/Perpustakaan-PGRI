@extends('layouts.dashboard')

@section('title', 'Dashboard Perpustakaan')
@section('page_heading', 'Dashboard Overview Perpustakaan')

@section('content')
<div class="space-y-6"
     x-data="{
         chartPeriod: 'monthly',
         monthlyData: {{ json_encode($chartMonthly) }},
         yearlyData: {{ json_encode($chartYearly) }},
         chartInstance: null,
         initChart() {
             const canvas = document.getElementById('sirkulasiLineChart');
             if (!canvas) return;

             const ctx = canvas.getContext('2d');
             const isMonthly = this.chartPeriod === 'monthly';
             const labels = isMonthly ? this.monthlyData.labels : this.yearlyData.labels;
             const loans = isMonthly ? this.monthlyData.loans : this.yearlyData.loans;
             const returns = isMonthly ? this.monthlyData.returns : this.yearlyData.returns;

             const gradientLoans = ctx.createLinearGradient(0, 0, 0, 280);
             gradientLoans.addColorStop(0, 'rgba(185, 28, 28, 0.20)');
             gradientLoans.addColorStop(1, 'rgba(185, 28, 28, 0.0)');

             const gradientReturns = ctx.createLinearGradient(0, 0, 0, 280);
             gradientReturns.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
             gradientReturns.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

             if (this.chartInstance) {
                 this.chartInstance.destroy();
             }

             this.chartInstance = new Chart(ctx, {
                 type: 'line',
                 data: {
                     labels: labels,
                     datasets: [
                         {
                             label: 'Peminjaman Buku',
                             data: loans,
                             borderColor: '#B91C1C',
                             borderWidth: 3,
                             backgroundColor: gradientLoans,
                             fill: true,
                             tension: 0.38,
                             pointBackgroundColor: '#FFFFFF',
                             pointBorderColor: '#B91C1C',
                             pointBorderWidth: 2.5,
                             pointRadius: 4,
                             pointHoverRadius: 7,
                             pointHoverBackgroundColor: '#B91C1C',
                             pointHoverBorderColor: '#FFFFFF',
                             pointHoverBorderWidth: 3
                         },
                         {
                             label: 'Pengembalian Buku',
                             data: returns,
                             borderColor: '#10B981',
                             borderWidth: 3,
                             backgroundColor: gradientReturns,
                             fill: true,
                             tension: 0.38,
                             pointBackgroundColor: '#FFFFFF',
                             pointBorderColor: '#10B981',
                             pointBorderWidth: 2.5,
                             pointRadius: 4,
                             pointHoverRadius: 7,
                             pointHoverBackgroundColor: '#10B981',
                             pointHoverBorderColor: '#FFFFFF',
                             pointHoverBorderWidth: 3
                         }
                     ]
                 },
                 options: {
                     responsive: true,
                     maintainAspectRatio: false,
                     interaction: {
                         intersect: false,
                         mode: 'index'
                     },
                     plugins: {
                         legend: {
                             display: false
                         },
                         tooltip: {
                             backgroundColor: '#18181B',
                             titleColor: '#9CA3AF',
                             titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                             bodyColor: '#FFFFFF',
                             bodyFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
                             padding: 12,
                             cornerRadius: 12,
                             boxPadding: 6,
                             usePointStyle: true,
                             callbacks: {
                                 label: function(context) {
                                     return ' ' + context.dataset.label + ': ' + context.parsed.y + ' Eks';
                                 }
                             }
                         }
                     },
                     scales: {
                         x: {
                             grid: {
                                 display: true,
                                 color: '#F3F4F6',
                                 borderDash: [3, 4]
                             },
                             ticks: {
                                 font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' },
                                 color: '#9CA3AF'
                             },
                             border: {
                                 display: false
                             }
                         },
                         y: {
                             beginAtZero: true,
                             grid: {
                                 color: '#F3F4F6',
                                 borderDash: [3, 4]
                             },
                             ticks: {
                                 precision: 0,
                                 font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' },
                                 color: '#9CA3AF',
                                 callback: function(value) {
                                     return value + ' Eks';
                                 }
                             },
                             border: {
                                 display: false
                             }
                         }
                     }
                 }
             });
         },
         setPeriod(p) {
             this.chartPeriod = p;
             this.initChart();
         }
     }"
     x-init="
         $nextTick(() => {
             if (typeof Chart === 'undefined') {
                 const script = document.createElement('script');
                 script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                 script.onload = () => initChart();
                 document.head.appendChild(script);
             } else {
                 initChart();
             }
         });
     ">

    <div>
        <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Sirkulasi Hari Ini ({{ date('d M Y') }})</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Peminjaman Hari Ini</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['peminjaman_hari_ini'] }} Peminjaman</span>
                    <span class="text-[10px] text-gray-400 font-medium">Buku dipinjam hari ini</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Buku Sedang Dipinjam</span>
                    <span class="text-2xl font-black text-brand-700 mt-1 block">{{ $stats['buku_sedang_dipinjam'] }} Buku</span>
                    <span class="text-[10px] text-gray-400 font-medium">Belum dikembalikan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Pengembalian Hari Ini</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $stats['pengembalian_hari_ini'] }} Pengembalian</span>
                    <span class="text-[10px] text-gray-400 font-medium">Sudah kembali ke rak</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

        </div>
    </div>

    <div>
        <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Master Koleksi &amp; Inventaris Fisik</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            <a href="{{ route('admin.buku') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Total Judul</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_judul'] }}</span>
                <span class="text-[9.5px] text-brand-700 font-bold block mt-0.5">Judul Buku &rarr;</span>
            </a>

            <a href="{{ route('admin.buku') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Total Fisik Buku</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_buku'] }}</span>
                <span class="text-[9.5px] text-emerald-600 font-bold block mt-0.5">{{ $stats['buku_tersedia'] }} Tersedia</span>
            </a>

            <a href="{{ route('admin.kategori') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Kategori</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_kategori'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Klasifikasi &rarr;</span>
            </a>

            <a href="{{ route('admin.penulis') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Penulis</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_penulis'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Pengarang &rarr;</span>
            </a>

            <a href="{{ route('admin.penerbit') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Penerbit</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_penerbit'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Percetakan &rarr;</span>
            </a>

            <a href="{{ route('admin.rak') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Rak Lokasi</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_rak'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Posisi Ruang &rarr;</span>
            </a>

        </div>
    </div>

    <div class="bg-white rounded-3xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Sirkulasi Buku</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight"
                          x-text="(chartPeriod === 'monthly' ? monthlyData.loans.reduce((a,b)=>a+b, 0) : yearlyData.loans.reduce((a,b)=>a+b, 0)) + ' Peminjaman'"></span>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span x-text="chartPeriod === 'monthly' ? '+' + monthlyData.returns.reduce((a,b)=>a+b, 0) + ' Pengembalian (Tahun ' + monthlyData.year + ')' : '+' + yearlyData.returns.reduce((a,b)=>a+b, 0) + ' Pengembalian (5 Tahun)'"></span>
                    </span>
                    <span class="text-xs text-gray-400 font-medium">• Database Terverifikasi</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3 text-xs font-bold mr-1">
                    <span class="inline-flex items-center gap-1.5 text-brand-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-700"></span>
                        <span>Peminjaman</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <span>Pengembalian</span>
                    </span>
                </div>

                <div class="inline-flex p-1 bg-gray-100/90 rounded-2xl border border-gray-200 shadow-2xs">
                    <button type="button" @click="setPeriod('monthly')"
                            class="px-4 py-1.5 text-xs font-black rounded-xl transition-all duration-200"
                            :class="chartPeriod === 'monthly' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-900'">
                        12 Bulan ({{ $chartMonthly['year'] }})
                    </button>
                    <button type="button" @click="setPeriod('yearly')"
                            class="px-4 py-1.5 text-xs font-black rounded-xl transition-all duration-200"
                            :class="chartPeriod === 'yearly' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-900'">
                        5 Tahun Terakhir
                    </button>
                </div>
            </div>
        </div>

        <div class="h-72 sm:h-80 w-full relative">
            <canvas id="sirkulasiLineChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase">Peminjaman Terbaru</h3>
                    <p class="text-[10.5px] text-gray-500">Daftar transaksi sirkulasi buku terkini</p>
                </div>
                <a href="{{ route('admin.peminjaman') }}" class="text-[11px] font-extrabold text-brand-700 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold">
                            <th class="py-2.5 px-4">Peminjam</th>
                            <th class="py-2.5 px-4">Judul Buku</th>
                            <th class="py-2.5 px-4 text-center">Jumlah</th>
                            <th class="py-2.5 px-4">Waktu</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                        @forelse($recentLoans as $loan)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-2.5 px-4">
                                    <p class="font-bold text-gray-900">{{ $loan->nama_peminjam ?: ($loan->user->name ?? '-') }}</p>
                                    @if($loan->jurusan)
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $loan->jurusan }}</p>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 max-w-xs truncate">{{ $loan->buku->judul ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-center font-bold">{{ $loan->jumlah }}</td>
                                <td class="py-2.5 px-4 text-gray-500 font-mono text-[10.5px]">{{ $loan->created_at->format('d M H:i') }}</td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($loan->status === 'dikembalikan')
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">KEMBALI</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DIPINJAM</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400 font-medium">Belum ada transaksi peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-4 flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-gray-900 uppercase">Log Aktivitas Sistem</h3>
                    <a href="{{ route('admin.audit-log') }}" class="text-[10px] font-extrabold text-brand-700 hover:underline">Semua</a>
                </div>
                <div class="divide-y divide-gray-100 mt-2 space-y-2">
                    @forelse($recentAuditLogs as $log)
                        <div class="pt-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 text-[11px]">{{ $log->user_name ?? 'Sistem' }}</span>
                                <span class="text-[9.5px] text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10.5px] text-gray-600 mt-0.5 line-clamp-1">{{ $log->deskripsi ?? $log->aktivitas }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
