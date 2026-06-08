<div class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <div class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Device</label>
            <select wire:model.live="deviceId" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}">{{ $device->id }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Status</label>
            <select wire:model.live="status" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                <option value="SAFE">SAFE</option>
                <option value="WARNING">WARNING</option>
                <option value="DANGER">DANGER</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Posisi Sensor</label>
            <select wire:model.live="sensorPosition" class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <option value="">Semua</option>
                <option value="left">Kiri</option>
                <option value="right">Kanan</option>
                <option value="back">Belakang</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Dari</label>
            <input type="date" wire:model.live="from"
                class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
        </div>
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Sampai</label>
            <input type="date" wire:model.live="to"
                class="rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2 text-xs text-[#F1F5F9] outline-none focus:border-blue-500/50">
        </div>
        <button wire:click="resetFilters" class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-[#94A3B8] transition-colors hover:text-[#F1F5F9]">Reset</button>
        <a href="{{ route('dashboard.history', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-green-400 transition-colors hover:text-green-300">
            Export CSV
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-700/30">
                    <th wire:click="sortBy('created_at')" class="cursor-pointer px-2 py-2 text-left font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Waktu
                        @if($sortField === 'created_at')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('device_id')" class="cursor-pointer px-2 py-2 text-left font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Device
                        @if($sortField === 'device_id')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('sensor_left')" class="cursor-pointer px-2 py-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Kiri
                        @if($sortField === 'sensor_left')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th class="px-2 py-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Kiri</th>
                    <th wire:click="sortBy('sensor_right')" class="cursor-pointer px-2 py-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Kanan
                        @if($sortField === 'sensor_right')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th class="px-2 py-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Kanan</th>
                    <th wire:click="sortBy('sensor_back')" class="cursor-pointer px-2 py-2 text-right font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Blkg
                        @if($sortField === 'sensor_back')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th class="px-2 py-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Blkg</th>
                    <th wire:click="sortBy('overall_status')" class="cursor-pointer px-2 py-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8] hover:text-[#F1F5F9]">
                        Overall
                        @if($sortField === 'overall_status')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th class="px-2 py-2 text-center font-['JetBrains_Mono'] text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($readings as $reading)
                <tr class="border-b border-slate-700/20 {{ $reading->overall_status === 'DANGER' ? 'bg-red-500/5' : ($reading->overall_status === 'WARNING' ? 'bg-yellow-500/5' : '') }}">
                    <td class="px-2 py-1.5 font-['JetBrains_Mono'] text-[#94A3B8]">{{ $reading->created_at->format('H:i:s') }}</td>
                    <td class="px-2 py-1.5 font-['JetBrains_Mono'] text-[#F1F5F9]">{{ $reading->device_id }}</td>
                    <td class="px-2 py-1.5 text-right font-['JetBrains_Mono']">{{ number_format($reading->sensor_left, 1) }}</td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="{{ $reading->status_left === 'DANGER' ? 'text-red-400' : ($reading->status_left === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }} font-['JetBrains_Mono'] text-[9px]">{{ substr($reading->status_left, 0, 4) }}</span>
                    </td>
                    <td class="px-2 py-1.5 text-right font-['JetBrains_Mono']">{{ number_format($reading->sensor_right, 1) }}</td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="{{ $reading->status_right === 'DANGER' ? 'text-red-400' : ($reading->status_right === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }} font-['JetBrains_Mono'] text-[9px]">{{ substr($reading->status_right, 0, 4) }}</span>
                    </td>
                    <td class="px-2 py-1.5 text-right font-['JetBrains_Mono']">{{ number_format($reading->sensor_back, 1) }}</td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="{{ $reading->status_back === 'DANGER' ? 'text-red-400' : ($reading->status_back === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }} font-['JetBrains_Mono'] text-[9px]">{{ substr($reading->status_back, 0, 4) }}</span>
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="{{ $reading->overall_status === 'DANGER' ? 'animate-pulse text-red-400 font-bold' : ($reading->overall_status === 'WARNING' ? 'text-yellow-400 font-medium' : 'text-green-400') }} font-['JetBrains_Mono'] text-[10px]">{{ $reading->overall_status }}</span>
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        <button wire:click="showDetail('{{ $reading->id }}')" class="rounded px-1.5 py-0.5 text-[10px] text-blue-400 hover:text-blue-300">Lihat</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $readings->links() }}
    </div>

    {{-- Detail Modal --}}
    @if($detailReading)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" wire:click.self="closeDetail">
        <div class="mx-4 w-full max-w-lg rounded-xl border border-slate-700/50 bg-[#1E293B] p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-['Hanken_Grotesk'] text-lg font-semibold text-[#F1F5F9]">Detail Pembacaan Sensor</h3>
                <button wire:click="closeDetail" class="rounded-lg p-1 text-[#94A3B8] transition-colors hover:text-[#F1F5F9]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Device</span>
                    <span class="font-['JetBrains_Mono'] text-[#F1F5F9]">{{ $detailReading->device_id }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Waktu</span>
                    <span class="font-['JetBrains_Mono'] text-[#F1F5F9]">{{ $detailReading->created_at->format('d M Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Sensor Kiri</span>
                    <span class="font-['JetBrains_Mono'] {{ $detailReading->status_left === 'DANGER' ? 'text-red-400' : ($detailReading->status_left === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }}">
                        {{ number_format($detailReading->sensor_left, 2) }} cm — {{ $detailReading->status_left }}
                    </span>
                </div>
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Sensor Kanan</span>
                    <span class="font-['JetBrains_Mono'] {{ $detailReading->status_right === 'DANGER' ? 'text-red-400' : ($detailReading->status_right === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }}">
                        {{ number_format($detailReading->sensor_right, 2) }} cm — {{ $detailReading->status_right }}
                    </span>
                </div>
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Sensor Belakang</span>
                    <span class="font-['JetBrains_Mono'] {{ $detailReading->status_back === 'DANGER' ? 'text-red-400' : ($detailReading->status_back === 'WARNING' ? 'text-yellow-400' : 'text-green-400') }}">
                        {{ number_format($detailReading->sensor_back, 2) }} cm — {{ $detailReading->status_back }}
                    </span>
                </div>
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">Overall Status</span>
                    <span class="inline-block rounded-full px-2.5 py-0.5 font-['JetBrains_Mono'] text-[11px] font-bold uppercase {{ $detailReading->overall_status === 'DANGER' ? 'animate-pulse bg-red-500/30 text-red-400' : ($detailReading->overall_status === 'WARNING' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-green-500/20 text-green-400') }}">
                        {{ $detailReading->overall_status }}
                    </span>
                </div>
                @if($detailReading->wifi_rssi)
                <div class="flex justify-between border-b border-slate-700/30 pb-2">
                    <span class="font-['JetBrains_Mono'] text-[11px] uppercase tracking-wider text-[#94A3B8]">WiFi RSSI</span>
                    <span class="font-['JetBrains_Mono'] text-[#F1F5F9]">{{ $detailReading->wifi_rssi }} dBm</span>
                </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <button wire:click="closeDetail" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>
