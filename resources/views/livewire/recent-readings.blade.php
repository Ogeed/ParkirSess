<div wire:poll.2000ms="refresh" class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Riwayat Real-time</h3>
        <a href="{{ route('dashboard.history') }}" class="text-xs text-blue-400 hover:text-blue-300">Lihat Semua →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-700/30">
                    <th class="pb-2 pr-2 text-left font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Waktu</th>
                    <th class="pb-2 pr-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Kiri</th>
                    <th class="pb-2 pr-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Kanan</th>
                    <th class="pb-2 pr-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Blkg</th>
                    <th class="pb-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($readings as $reading)
                <tr class="border-b border-slate-700/20 transition-colors hover:bg-slate-700/10 {{ $reading['overall_status'] === 'DANGER' ? 'bg-red-500/5' : ($reading['overall_status'] === 'WARNING' ? 'bg-yellow-500/5' : '') }}">
                    <td class="py-1.5 pr-2 font-['JetBrains_Mono'] text-[#94A3B8]">{{ \Carbon\Carbon::parse($reading['created_at'])->format('H:i:s') }}</td>
                    <td class="py-1.5 pr-2 text-right font-['JetBrains_Mono'] font-medium text-[#F1F5F9]">{{ number_format($reading['sensor_left'], 1) }}</td>
                    <td class="py-1.5 pr-2 text-right font-['JetBrains_Mono'] font-medium text-[#F1F5F9]">{{ number_format($reading['sensor_right'], 1) }}</td>
                    <td class="py-1.5 pr-2 text-right font-['JetBrains_Mono'] font-medium text-[#F1F5F9]">{{ number_format($reading['sensor_back'], 1) }}</td>
                    <td class="py-1.5 text-center">
                        <span class="{{ $reading['overall_status'] === 'DANGER' ? 'animate-pulse text-red-400' : ($reading['overall_status'] === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }} font-['JetBrains_Mono'] text-[10px] font-bold">
                            {{ $reading['overall_status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-[#94A3B8]">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
