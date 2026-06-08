@extends('layouts.app')

@section('title', 'Riwayat — SmartPark IoT')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="font-['Hanken_Grotesk'] text-2xl font-semibold">Riwayat Data Sensor</h1>
        <p class="text-sm text-[#94A3B8]">Data historis pembacaan sensor parkir</p>
    </div>
    <a href="{{ route('dashboard.history', array_merge(request()->query(), ['export' => 'csv'])) }}"
        class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-2 text-sm font-medium text-green-400 transition-colors hover:bg-green-500/20">
        Export CSV
    </a>
</div>

<div class="mb-4 rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Device</label>
            <select name="device_id" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>{{ $device->id }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</label>
            <select name="status" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                <option value="SAFE" {{ request('status') == 'SAFE' ? 'selected' : '' }}>SAFE</option>
                <option value="WARNING" {{ request('status') == 'WARNING' ? 'selected' : '' }}>WARNING</option>
                <option value="DANGER" {{ request('status') == 'DANGER' ? 'selected' : '' }}>DANGER</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Posisi Sensor</label>
            <select name="sensor_position" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                <option value="left" {{ request('sensor_position') == 'left' ? 'selected' : '' }}>Kiri</option>
                <option value="right" {{ request('sensor_position') == 'right' ? 'selected' : '' }}>Kanan</option>
                <option value="back" {{ request('sensor_position') == 'back' ? 'selected' : '' }}>Belakang</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Dari</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Sampai</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Per Halaman</label>
            <select name="per_page" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">Filter</button>
        <a href="{{ route('dashboard.history') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-[#94A3B8] transition-colors hover:text-[#F1F5F9]">Reset</a>
    </form>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-700/50 bg-[#1E293B]">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-700/50">
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Waktu</th>
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Device</th>
                <th class="px-4 py-3 text-right font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Kiri</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Kiri</th>
                <th class="px-4 py-3 text-right font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Kanan</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Kanan</th>
                <th class="px-4 py-3 text-right font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Belakang</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Blkg</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Overall</th>
            </tr>
        </thead>
        <tbody>
            @forelse($readings as $reading)
            <tr class="border-b border-slate-700/30 transition-colors hover:bg-slate-700/20 {{ $reading->overall_status === 'DANGER' ? 'bg-red-500/5' : ($reading->overall_status === 'WARNING' ? 'bg-yellow-500/5' : '') }}">
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $reading->created_at->format('H:i:s') }}</td>
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#F1F5F9]">{{ $reading->device_id }}</td>
                <td class="px-4 py-3 text-right font-['JetBrains_Mono'] text-sm">{{ number_format($reading->sensor_left, 1) }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block rounded-full px-2 py-0.5 font-['JetBrains_Mono'] text-[10px] font-medium uppercase {{ $reading->status_left === 'DANGER' ? 'bg-red-500/20 text-red-400' : ($reading->status_left === 'WARNING' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ substr($reading->status_left, 0, 4) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-['JetBrains_Mono'] text-sm">{{ number_format($reading->sensor_right, 1) }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block rounded-full px-2 py-0.5 font-['JetBrains_Mono'] text-[10px] font-medium uppercase {{ $reading->status_right === 'DANGER' ? 'bg-red-500/20 text-red-400' : ($reading->status_right === 'WARNING' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ substr($reading->status_right, 0, 4) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-['JetBrains_Mono'] text-sm">{{ number_format($reading->sensor_back, 1) }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block rounded-full px-2 py-0.5 font-['JetBrains_Mono'] text-[10px] font-medium uppercase {{ $reading->status_back === 'DANGER' ? 'bg-red-500/20 text-red-400' : ($reading->status_back === 'WARNING' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ substr($reading->status_back, 0, 4) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block rounded-full px-2.5 py-0.5 font-['JetBrains_Mono'] text-[10px] font-bold uppercase {{ $reading->overall_status === 'DANGER' ? 'animate-pulse bg-red-500/30 text-red-400' : ($reading->overall_status === 'WARNING' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ $reading->overall_status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-sm text-[#94A3B8]">Belum ada data sensor</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $readings->links() }}
</div>
@endsection
