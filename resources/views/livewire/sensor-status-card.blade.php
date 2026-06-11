<div wire:poll.1000ms="refresh" class="grid grid-cols-2 gap-4 xl:grid-cols-4">
    @php
        $positions = [
            ['label' => 'KIRI', 'value' => $latestReading?->sensor_left, 'status' => $latestReading?->status_left, 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9'],
            ['label' => 'KANAN', 'value' => $latestReading?->sensor_right, 'status' => $latestReading?->status_right, 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9'],
            ['label' => 'BELAKANG', 'value' => $latestReading?->sensor_back, 'status' => $latestReading?->status_back, 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9'],
            ['label' => 'PERANGKAT', 'value' => null, 'status' => $device?->is_online ? 'ONLINE' : 'OFFLINE', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ];
    @endphp

    @foreach($positions as $pos)
        <div class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4 transition-all hover:border-slate-600/50">
            <div class="flex items-center justify-between">
                <span class="font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">{{ $pos['label'] }}</span>
                <svg class="h-4 w-4 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $pos['icon'] }}"/>
                </svg>
            </div>
            @if($pos['value'] !== null)
                <div class="mt-2 font-['Hanken_Grotesk'] text-3xl font-bold tracking-tight text-[#F1F5F9]">
                    {{ number_format($pos['value'], 1) }}
                    <span class="text-lg font-normal text-[#94A3B8]">cm</span>
                </div>
            @else
                <div class="mt-2 font-['Hanken_Grotesk'] text-3xl font-bold tracking-tight text-[#F1F5F9]">
                    {{ $device?->id ?? 'N/A' }}
                </div>
            @endif
            <div class="mt-2">
                @php
                    $isDanger = $pos['status'] === 'DANGER' || $pos['status'] === 'ONLINE';
                    $isOnline = $pos['status'] === 'ONLINE';
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-['JetBrains_Mono'] text-[11px] font-bold uppercase tracking-wider
                    @if($pos['status'] === 'DANGER') bg-red-500/20 text-red-400 animate-pulse border border-red-500/30
                    @elseif($pos['status'] === 'WARNING') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                    @elseif($pos['status'] === 'SAFE') bg-green-500/20 text-green-400 border border-green-500/30
                    @elseif($pos['status'] === 'ONLINE')
                        bg-green-500/20 text-green-400 border border-green-500/30
                    @else bg-slate-500/20 text-slate-400 border border-slate-500/30
                    @endif
                ">
                    @if($pos['status'] === 'ONLINE')
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        </span>
                    @endif
                    {{ $pos['status'] ?? 'N/A' }}
                </span>
            </div>
        </div>
    @endforeach
</div>
