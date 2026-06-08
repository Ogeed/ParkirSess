<div wire:poll.5000ms="refresh" class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <h3 class="mb-4 font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Status Perangkat</h3>

    @forelse($devices as $device)
        <div class="mb-3 rounded-lg border border-slate-700/30 bg-[#0F172A]/50 p-3 transition-all hover:border-slate-600/50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-['JetBrains_Mono'] text-sm font-medium text-[#F1F5F9]">{{ $device['id'] }}</div>
                    <div class="mt-0.5 font-['Inter'] text-xs text-[#94A3B8]">{{ $device['name'] }}</div>
                </div>
                @if($device['is_online'])
                    <span class="relative flex h-3 w-3">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-green-500"></span>
                    </span>
                @else
                    <span class="relative flex h-3 w-3">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-red-500"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                    </span>
                @endif
            </div>
            <div class="mt-2 flex gap-4 text-[11px] text-[#94A3B8]">
                <span class="font-['JetBrains_Mono']">
                    RSSI: {{ $device['wifi_rssi'] ?? 'N/A' }}
                </span>
                <span class="font-['JetBrains_Mono']">
                    {{ $device['last_seen'] ? \Carbon\Carbon::parse($device['last_seen'])->diffForHumans() : 'Never' }}
                </span>
            </div>
        </div>
    @empty
        <div class="py-4 text-center text-sm text-[#94A3B8]">Belum ada perangkat terdaftar</div>
    @endforelse
</div>
