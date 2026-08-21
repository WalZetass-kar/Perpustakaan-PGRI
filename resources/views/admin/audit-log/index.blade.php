@extends('layouts.dashboard')

@section('title', 'Audit Log Sistem')
@section('page_heading', 'Catatan Aktivitas Audit Log')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Jejak Aktivitas Keamanan & Perubahan Data</h2>
    </div>
    <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Waktu Stempel</th>
                    <th class="py-3 px-5 font-semibold">Pengguna</th>
                    <th class="py-3 px-5 font-semibold">Jenis Aktivitas</th>
                    <th class="py-3 px-5 font-semibold">Rincian Deskripsi</th>
                    <th class="py-3 px-5 font-semibold">IP Client</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="py-3.5 px-5 font-bold text-gray-900">{{ $log->user_name ?? 'System' }}</td>
                        <td class="py-3.5 px-5 font-mono font-bold text-brand-700">{{ $log->aktivitas }}</td>
                        <td class="py-3.5 px-5 text-gray-600">{{ $log->deskripsi }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y divide-gray-100">
        @forelse($logs as $log)
            @php
                $aksi = $log->aktivitas ?? '';
                if (str_contains($aksi, 'HAPUS')) {
                    [$dotColor, $icon] = ['bg-rose-50 text-rose-600 border-rose-200', 'fa-trash-can'];
                } elseif (str_contains($aksi, 'TAMBAH')) {
                    [$dotColor, $icon] = ['bg-emerald-50 text-emerald-600 border-emerald-200', 'fa-plus'];
                } elseif (str_contains($aksi, 'UPDATE')) {
                    [$dotColor, $icon] = ['bg-amber-50 text-amber-600 border-amber-200', 'fa-pen'];
                } elseif (str_contains($aksi, 'GAGAL')) {
                    [$dotColor, $icon] = ['bg-rose-50 text-rose-600 border-rose-200', 'fa-triangle-exclamation'];
                } elseif (str_contains($aksi, 'LOGIN') || str_contains($aksi, 'LOGOUT')) {
                    [$dotColor, $icon] = ['bg-blue-50 text-blue-600 border-blue-200', 'fa-right-to-bracket'];
                } elseif (str_contains($aksi, 'TRANSAKSI') || str_contains($aksi, 'APPROVE') || str_contains($aksi, 'PENGAJUAN')) {
                    [$dotColor, $icon] = ['bg-blue-50 text-blue-600 border-blue-200', 'fa-arrows-rotate'];
                } else {
                    [$dotColor, $icon] = ['bg-gray-100 text-gray-500 border-gray-200', 'fa-circle-info'];
                }
            @endphp
            <div class="p-4 flex items-start gap-2.5">
                <div class="w-8 h-8 rounded-lg border flex items-center justify-center shrink-0 {{ $dotColor }}">
                    <i class="fa-solid {{ $icon }} text-[11px]"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-mono font-bold text-brand-700 text-[11px]">{{ $log->aktivitas }}</span>
                        <span class="shrink-0 text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="font-bold text-gray-900 text-xs mt-1">{{ $log->user_name ?? 'System' }}</p>
                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">{{ $log->deskripsi }}</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-1">IP: {{ $log->ip_address ?? '127.0.0.1' }}</p>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-gray-400 font-medium text-xs px-4">Belum ada catatan aktivitas.</div>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
@endsection
