<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'SmartPark IoT') — Easypark</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');
    </style>
</head>
<body class="bg-[#0F172A] text-[#F1F5F9] font-['Inter'] antialiased">
    <nav class="sticky top-0 z-50 border-b border-slate-700/50 bg-[#0F172A]/95 backdrop-blur-sm">
        <div class="mx-auto flex h-14 items-center justify-between px-4 md:h-16 md:px-8 lg:max-w-[1440px]">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-500/20 md:h-8 md:w-8">
                    <svg class="h-4 w-4 text-blue-400 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="font-['Hanken_Grotesk'] text-base font-semibold text-[#F1F5F9] md:text-lg">SmartPark IoT</span>
                @auth
                    <span class="hidden rounded-full bg-slate-800 px-2 py-0.5 font-['JetBrains_Mono'] text-[10px] text-[#94A3B8] sm:inline-block md:text-[11px]">v1.0.0</span>
                @endauth
            </div>

            @auth
            <div class="flex items-center gap-2 md:gap-4">
                <div class="hidden items-center gap-1.5 sm:flex">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                    </span>
                    <span class="font-['JetBrains_Mono'] text-[10px] text-[#94A3B8] md:text-xs">LIVE</span>
                </div>

                {{-- Mobile menu toggle --}}
                <button id="navToggle" class="flex rounded-lg p-1.5 text-[#94A3B8] transition-colors hover:text-[#F1F5F9] lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="hidden items-center gap-3 md:flex">
                    <span class="hidden text-sm text-[#94A3B8] md:inline-block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-700 px-2 py-1 text-xs text-[#94A3B8] transition-colors hover:border-slate-600 hover:text-[#F1F5F9] md:px-3 md:py-1.5 md:text-sm">Logout</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    @auth
    {{-- Desktop nav tabs --}}
    <div class="hidden border-b border-slate-700/50 lg:block">
        <div class="mx-auto flex max-w-[1440px] items-center gap-1 px-8 py-3">
            <a href="{{ route('dashboard.index') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.index') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                Dashboard
            </a>
            <a href="{{ route('dashboard.history') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.history') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                Riwayat
            </a>
            <a href="{{ route('dashboard.devices') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.devices') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                Perangkat
            </a>
            <a href="{{ route('dashboard.alerts') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.alerts') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                Alert
            </a>
            <a href="{{ route('dashboard.settings') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                Pengaturan
            </a>
        </div>
    </div>

    {{-- Mobile nav drawer --}}
    <div id="mobileNav" class="hidden border-b border-slate-700/50 bg-[#0F172A]/95 lg:hidden">
        <div class="flex flex-col gap-1 px-4 py-3">
            <a href="{{ route('dashboard.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.index') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('dashboard.history') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.history') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                📋 Riwayat
            </a>
            <a href="{{ route('dashboard.devices') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.devices') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                ⚙️ Perangkat
            </a>
            <a href="{{ route('dashboard.alerts') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.alerts') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                🔔 Alert
            </a>
            <a href="{{ route('dashboard.settings') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-blue-500/20 text-blue-400' : 'text-[#94A3B8] hover:text-[#F1F5F9]' }}">
                ⚡ Pengaturan
            </a>
            <div class="mt-2 border-t border-slate-700/50 pt-2">
                <span class="block px-3 py-1 text-xs text-[#94A3B8]">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-red-400 transition-colors hover:bg-red-500/10">Logout</button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <main class="mx-auto px-4 py-4 md:px-8 md:py-6 lg:max-w-[1440px]">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 px-3 py-2 text-sm text-green-400 md:px-4 md:py-3">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-400 md:px-4 md:py-3">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('navToggle');
            const mobileNav = document.getElementById('mobileNav');
            if (toggle && mobileNav) {
                toggle.addEventListener('click', function() {
                    mobileNav.classList.toggle('hidden');
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
