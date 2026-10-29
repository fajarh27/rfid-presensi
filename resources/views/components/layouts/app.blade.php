<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} · Absensi RFID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@php
    $nav = [
        ['dashboard', 'Dashboard'],
        ['attendances', 'Data Absensi'],
        ['employees', 'Peserta'],
        ['cards', 'Kartu RFID'],
        ['schedules', 'Jam Kerja'],
        ['devices', 'Perangkat'],
    ];
@endphp
<div class="min-h-screen md:flex">
    <aside class="flex gap-1 overflow-x-auto bg-slate-900 p-3 text-slate-300 md:min-h-screen md:w-60 md:flex-col md:p-4">
        <div class="hidden px-3 pb-4 text-lg font-bold text-white md:block">Absensi RFID</div>
        @foreach ($nav as [$route, $label])
            <a href="{{ route($route) }}" wire:navigate
               class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($route) ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}" class="md:mt-auto">
            @csrf
            <button class="w-full whitespace-nowrap rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-slate-800">Keluar</button>
        </form>
    </aside>

    <main class="flex-1 p-4 md:p-8">
        <h1 class="mb-6 text-2xl font-bold">{{ $title ?? 'Dashboard' }}</h1>
        {{ $slot }}
    </main>
</div>
</body>
</html>
