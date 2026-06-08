@extends('layouts.app')

@section('title', 'Pengaturan — SmartPark IoT')

@section('content')
<div class="mb-6">
    <h1 class="font-['Hanken_Grotesk'] text-2xl font-semibold">Pengaturan</h1>
    <p class="text-sm text-[#94A3B8]">Konfigurasi threshold dan akun</p>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-6">
        <h2 class="mb-4 font-['Hanken_Grotesk'] text-lg font-semibold">Threshold Jarak</h2>
        <form method="POST" action="{{ route('dashboard.settings.update-threshold') }}">
            @csrf
            <div class="mb-4">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Jarak Aman (cm)</label>
                <input type="number" name="threshold_safe" value="{{ config('sensor.threshold_safe') }}"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <p class="mt-1 text-xs text-[#94A3B8]">> nilai ini = SAFE (Hijau)</p>
            </div>
            <div class="mb-6">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Jarak Waspada (cm)</label>
                <input type="number" name="threshold_warning" value="{{ config('sensor.threshold_warning') }}"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
                <p class="mt-1 text-xs text-[#94A3B8]">> nilai ini = WARNING (Kuning), <= bahaya (Merah)</p>
            </div>
            <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">Simpan Threshold</button>
        </form>
    </div>

    <div class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-6">
        <h2 class="mb-4 font-['Hanken_Grotesk'] text-lg font-semibold">Pengaturan Akun</h2>
        <form method="POST" action="{{ route('dashboard.settings.update-account') }}">
            @csrf
            <div class="mb-4">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Nama</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50" required>
            </div>
            <div class="mb-4">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50" required>
            </div>
            <div class="mb-4">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Password Baru (opsional)</label>
                <input type="password" name="password"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
            </div>
            <div class="mb-6">
                <label class="mb-1.5 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-lg border border-slate-700 bg-[#0F172A] px-3 py-2.5 text-sm text-[#F1F5F9] outline-none focus:border-blue-500/50">
            </div>
            <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">Simpan Akun</button>
        </form>
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-700/50 bg-[#1E293B] p-6">
    <h2 class="mb-4 font-['Hanken_Grotesk'] text-lg font-semibold">API Settings</h2>
    <div class="space-y-3">
        <div>
            <label class="mb-1 block font-['JetBrains_Mono'] text-xs font-medium uppercase tracking-wider text-[#94A3B8]">API Base URL</label>
            <code class="block rounded-lg bg-[#0F172A] px-3 py-2 font-['JetBrains_Mono'] text-sm text-blue-400">{{ url('/api/v1') }}</code>
        </div>
    </div>
</div>
@endsection
