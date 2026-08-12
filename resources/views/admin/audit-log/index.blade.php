@extends('layouts.dashboard')

@section('title', 'Audit Log Sistem')
@section('page_heading', 'Catatan Aktivitas Audit Log')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Jejak Aktivitas Keamanan & Perubahan Data</h2>
    </div>
    <div class="overflow-x-auto">
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
    <div class="p-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
@endsection
