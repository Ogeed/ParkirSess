@extends('layouts.app')

@section('title', 'Alert — SmartPark IoT')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="font-['Hanken_Grotesk'] text-2xl font-semibold">Alert & Peringatan</h1>
        <p class="text-sm text-[#94A3B8]">Riwayat peringatan sistem sensor parkir</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('dashboard.alerts.acknowledge-all') }}">
            @csrf
            <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">
                Tandai Semua
            </button>
        </form>
    </div>
</div>

<div class="mb-4 rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Tipe Alert</label>
            <select name="alert_type" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                @foreach($alertTypes as $type)
                    <option value="{{ $type }}" {{ request('alert_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
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
        <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">Filter</button>
        <a href="{{ route('dashboard.alerts') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-[#94A3B8] transition-colors hover:text-[#F1F5F9]">Reset</a>
    </form>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-700/50 bg-[#1E293B]">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-700/50">
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Waktu</th>
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Device</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Tipe</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Posisi</th>
                <th class="px-4 py-3 text-right font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Jarak</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alerts as $alert)
            <tr class="border-b border-slate-700/30 transition-colors hover:bg-slate-700/20 {{ !$alert->is_acknowledged && $alert->alert_type === 'DANGER' ? 'bg-red-500/5' : '' }}">
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $alert->created_at->format('H:i:s') }}</td>
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#F1F5F9]">{{ $alert->device_id }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block rounded-full px-2.5 py-0.5 font-['JetBrains_Mono'] text-[10px] font-medium {{ $alert->alert_type === 'DANGER' ? 'animate-pulse bg-red-500/30 text-red-400' : ($alert->alert_type === 'DEVICE_OFFLINE' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ $alert->alert_type }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $alert->sensor_position ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-['JetBrains_Mono'] text-sm">
                    {{ $alert->distance_value ? number_format($alert->distance_value, 1) . ' cm' : '-' }}
                </td>
                <td class="px-4 py-3 text-center">
                    @if($alert->is_acknowledged)
                        <span class="text-xs text-[#94A3B8]">Selesai</span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs text-yellow-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-400"></span>
                            Baru
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if(!$alert->is_acknowledged)
                        <form method="POST" action="{{ route('dashboard.alerts.acknowledge', $alert->id) }}">
                            @csrf
                            <button type="submit" class="rounded px-2 py-1 text-xs text-blue-400 hover:text-blue-300">Tandai</button>
                        </form>
                    @else
                        <span class="text-xs text-[#94A3B8]">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-sm text-[#94A3B8]">Belum ada alert</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $alerts->links() }}
</div>
@endsection
