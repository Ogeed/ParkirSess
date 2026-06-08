@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md px-6">
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500/20">
            <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h1 class="font-['Hanken_Grotesk'] text-2xl font-semibold text-[#F1F5F9]">SmartPark IoT</h1>
        <p class="mt-1 text-sm text-[#94A3B8]">Masuk ke dashboard monitoring</p>
    </div>

    <div class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-6">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] placeholder-[#94A3B8] outline-none transition-colors focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30"
                    placeholder="admin@easypark.dev" required autofocus>
                @error('email')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Password</label>
                <input type="password" name="password"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] placeholder-[#94A3B8] outline-none transition-colors focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30"
                    placeholder="••••••••" required>
                @error('password')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-600 focus:ring-2 focus:ring-blue-500/30">
                Masuk
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-[#94A3B8]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300">Daftar</a>
        </p>
    </div>

    <p class="mt-6 text-center text-xs text-[#94A3B8]">
        SmartPark IoT — Sistem Sensor Parkir Kendaraan
    </p>
</div>
@endsection
