@extends('layouts.app')

@section('title', 'Perangkat — SmartPark IoT')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="font-['Hanken_Grotesk'] text-2xl font-semibold">Manajemen Perangkat</h1>
        <p class="text-sm text-[#94A3B8]">Daftar perangkat ESP32 yang terdaftar</p>
    </div>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-700/50 bg-[#1E293B]">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-700/50">
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Device ID</th>
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Nama</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Last Seen</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Firmware</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">RSSI</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devices as $device)
            <tr class="border-b border-slate-700/30 transition-colors hover:bg-slate-700/20">
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#F1F5F9]">{{ $device->id }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('dashboard.devices.update', $device->id) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $device->name }}"
                            class="rounded-lg border border-slate-700 bg-[#0F172A] px-2 py-1 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
                        <button type="submit" class="rounded px-2 py-1 text-xs text-blue-400 hover:text-blue-300">Simpan</button>
                    </form>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($device->is_online)
                        <span class="relative inline-flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                            </span>
                            <span class="font-['JetBrains_Mono'] text-xs text-green-400">ONLINE</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            <span class="font-['JetBrains_Mono'] text-xs text-red-400">OFFLINE</span>
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center font-['JetBrains_Mono'] text-xs text-[#94A3B8]">
                    {{ $device->last_seen ? $device->last_seen->diffForHumans() : '-' }}
                </td>
                <td class="px-4 py-3 text-center font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $device->firmware_version ?? '-' }}</td>
                <td class="px-4 py-3 text-center font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $device->wifi_rssi ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('dashboard.devices.reset-token', $device->id) }}"
                            class="rounded px-2 py-1 text-xs text-yellow-400 hover:text-yellow-300"
                            onclick="return confirm('Reset token?')">Reset Token</a>
                        <form method="POST" action="{{ route('dashboard.devices.destroy', $device->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded px-2 py-1 text-xs text-red-400 hover:text-red-300"
                                onclick="return confirm('Hapus perangkat?')">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-sm text-[#94A3B8]">Belum ada perangkat terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
