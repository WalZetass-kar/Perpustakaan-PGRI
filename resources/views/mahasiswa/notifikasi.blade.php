@extends('layouts.dashboard')

@section('title', 'Notifikasi Saya')
@section('page_heading', 'Notifikasi Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-base font-bold text-gray-900 mb-4">Kotak Masuk Notifikasi System</h2>
        <div class="space-y-3">
            @forelse($notifications as $notif)
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-gray-900">{{ $notif->judul }}</h3>
                            <span class="text-[10px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $notif->pesan }}</p>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 text-xs">
                    Belum ada pesan notifikasi baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
