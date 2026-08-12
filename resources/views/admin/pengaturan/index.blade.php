@extends('layouts.dashboard')

@section('title', 'Pengaturan Aturan')
@section('page_heading', 'Pengaturan Aturan Peminjaman & Denda')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
    <div class="border-b border-gray-100 pb-4">
        <h2 class="text-base font-bold text-gray-900">Aturan Peminjaman & Sanksi Denda</h2>
        <p class="text-xs text-gray-500">Ubah aturan operasional tanpa perlu mengubah baris kode program.</p>
    </div>

    <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        
        @foreach($settings as $setting)
            <div>
                <label class="block font-bold text-gray-800 mb-1">{{ $setting->label }}</label>
                @if($setting->tipe === 'number')
                    <input type="number" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-mono">
                @else
                    <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
                @endif
            </div>
        @endforeach

        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-bold text-xs rounded-lg hover:bg-brand-800 transition">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection
