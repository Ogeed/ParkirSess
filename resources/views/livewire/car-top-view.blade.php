<div wire:poll.1000ms="refresh" class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <h3 class="mb-4 font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Top View — Visualisasi Sensor</h3>

    <div class="flex flex-col items-center">
        <svg viewBox="0 0 400 300" class="w-full max-w-sm">
            @php
                $leftDist = $latestReading?->sensor_left ?? 50;
                $rightDist = $latestReading?->sensor_right ?? 50;
                $backDist = $latestReading?->sensor_back ?? 50;
                $maxDist = 100;

                $leftPct = max(0, min($leftDist / $maxDist, 1));
                $rightPct = max(0, min($rightDist / $maxDist, 1));
                $backPct = max(0, min($backDist / $maxDist, 1));

                $leftColor = $leftDist > 50 ? '#22C55E' : ($leftDist >= 20 ? '#EAB308' : '#EF4444');
                $rightColor = $rightDist > 50 ? '#22C55E' : ($rightDist >= 20 ? '#EAB308' : '#EF4444');
                $backColor = $backDist > 50 ? '#22C55E' : ($backDist >= 20 ? '#EAB308' : '#EF4444');
            @endphp

            <defs>
                <linearGradient id="carGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#1E293B"/>
                    <stop offset="100%" stop-color="#0F172A"/>
                </linearGradient>
            </defs>

            <rect x="80" y="80" width="240" height="140" rx="20" fill="url(#carGrad)" stroke="#334155" stroke-width="2"/>

            <text x="200" y="165" text-anchor="middle" font-family="Hanken Grotesk" font-size="20" font-weight="700" fill="#F1F5F9">MOBIL</text>
            <text x="200" y="185" text-anchor="middle" font-family="JetBrains Mono" font-size="11" fill="#94A3B8">Top View</text>

            <rect x="80" y="55" width="100" height="20" rx="4" fill="{{ $leftColor }}" opacity="0.3"/>
            <rect x="80" y="55" width="{{ 100 * $leftPct }}" height="20" rx="4" fill="{{ $leftColor }}" opacity="0.8">
                <animate attributeName="opacity" values="0.8;0.5;0.8" dur="2s" repeatCount="indefinite"/>
            </rect>
            <text x="130" y="48" text-anchor="middle" font-family="JetBrains Mono" font-size="11" fill="{{ $leftColor }}" font-weight="bold">{{ number_format($leftDist, 1) }}cm</text>
            <text x="80" y="45" font-family="JetBrains Mono" font-size="10" fill="#94A3B8">◄ KIRI</text>

            <rect x="220" y="55" width="100" height="20" rx="4" fill="{{ $rightColor }}" opacity="0.3"/>
            <rect x="{{ 320 - (100 * $rightPct) }}" y="55" width="{{ 100 * $rightPct }}" height="20" rx="4" fill="{{ $rightColor }}" opacity="0.8">
                <animate attributeName="opacity" values="0.8;0.5;0.8" dur="2s" repeatCount="indefinite"/>
            </rect>
            <text x="280" y="48" text-anchor="middle" font-family="JetBrains Mono" font-size="11" fill="{{ $rightColor }}" font-weight="bold">{{ number_format($rightDist, 1) }}cm</text>
            <text x="315" y="45" font-family="JetBrains Mono" font-size="10" fill="#94A3B8">KANAN ►</text>

            <rect x="155" y="225" width="90" height="20" rx="4" fill="{{ $backColor }}" opacity="0.3"/>
            <rect x="155" y="225" width="90" height="20" rx="4" fill="{{ $backColor }}" opacity="0.8"
                style="clip-path: inset(0 0 0 {{ (1 - $backPct) * 100 }}%)">
                <animate attributeName="opacity" values="0.8;0.5;0.8" dur="2s" repeatCount="indefinite"/>
            </rect>

            <polygon points="190,225 210,225 200,215" fill="{{ $backColor }}" opacity="0.8">
                <animate attributeName="opacity" values="0.8;0.3;0.8" dur="1s" repeatCount="indefinite"/>
            </polygon>

            <text x="200" y="218" text-anchor="middle" font-family="JetBrains Mono" font-size="11" fill="{{ $backColor }}" font-weight="bold">{{ number_format($backDist, 1) }}cm</text>
            <text x="200" y="260" text-anchor="middle" font-family="JetBrains Mono" font-size="10" fill="#94A3B8">▼ BELAKANG</text>
        </svg>
    </div>

    <div class="mt-3 flex justify-center gap-4 text-xs text-[#94A3B8]">
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-500"></span> Aman</span>
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-yellow-500"></span> Waspada</span>
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-red-500"></span> Bahaya</span>
    </div>
</div>
