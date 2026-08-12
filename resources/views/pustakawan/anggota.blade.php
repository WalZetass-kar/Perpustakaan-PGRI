@extends('layouts.dashboard')

@section('title', 'Data Anggota')
@section('page_heading', 'Data Anggota Perpustakaan')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Daftar Anggota Civitas Akademika</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Nomor Anggota</th>
                    <th class="py-3 px-5 font-semibold">Nama Mahasiswa</th>
                    <th class="py-3 px-5 font-semibold">NIM</th>
                    <th class="py-3 px-5 font-semibold">Program Studi</th>
                    <th class="py-3 px-5 font-semibold">Email</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($anggotaList as $member)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $member->nomor_anggota }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $member->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono">{{ $member->nim ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $member->program_studi ?? '-' }}</td>
                        <td class="py-3.5 px-5 text-gray-500">{{ $member->user->email ?? '-' }}</td>
                        <td class="py-3.5 px-5">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">{{ $member->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $anggotaList->links() }}
    </div>
</div>
@endsection
