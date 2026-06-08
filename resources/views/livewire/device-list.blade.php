<div wire:poll.10000ms="refresh" class="overflow-x-auto rounded-xl border border-slate-700/50 bg-[#1E293B]">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-700/50">
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Device ID</th>
                <th class="px-4 py-3 text-left font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Nama</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Last Seen</th>
                <th class="px-4 py-3 text-center font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">RSSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devices as $device)
            <tr class="border-b border-slate-700/30 transition-colors hover:bg-slate-700/20">
                <td class="px-4 py-3 font-['JetBrains_Mono'] text-xs text-[#F1F5F9]">{{ $device['id'] }}</td>
                <td class="px-4 py-3 text-[#F1F5F9]">{{ $device['name'] }}</td>
                <td class="px-4 py-3 text-center">
                    @if($device['is_online'])
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
                    {{ $device['last_seen'] ? \Carbon\Carbon::parse($device['last_seen'])->diffForHumans() : '-' }}
                </td>
                <td class="px-4 py-3 text-center font-['JetBrains_Mono'] text-xs text-[#94A3B8]">{{ $device['wifi_rssi'] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm text-[#94A3B8]">Belum ada perangkat terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
