<div wire:poll.10s class="space-y-6">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card"><div class="text-sm text-slate-500">Peserta aktif</div><div class="mt-1 text-3xl font-bold">{{ $total }}</div></div>
        <div class="card"><div class="text-sm text-slate-500">Hadir hari ini</div><div class="mt-1 text-3xl font-bold text-green-600">{{ $present }}</div></div>
        <div class="card"><div class="text-sm text-slate-500">Terlambat</div><div class="mt-1 text-3xl font-bold text-amber-600">{{ $late }}</div></div>
        <div class="card">
            <div class="text-sm text-slate-500">Scan kartu tak dikenal</div>
            <div class="mt-1 text-3xl font-bold text-red-600">{{ $unknown }}</div>
            @if ($unknown)<a href="{{ route('cards') }}" wire:navigate class="text-xs text-indigo-600 hover:underline">Beri nama kartu →</a>@endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <h2 class="mb-3 font-semibold">Scan terbaru</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead><tr><th class="th">Waktu</th><th class="th">Nama</th><th class="th">Jenis</th><th class="th">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($latest as $a)
                        <tr>
                            <td class="td whitespace-nowrap">{{ $a->scanned_at->format('d/m H:i:s') }}</td>
                            <td class="td">{{ $a->employee?->name ?? $a->rfid_uid }}</td>
                            <td class="td">{{ $a->type_label }}</td>
                            <td class="td"><span class="badge {{ $a->status_class }}">{{ $a->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="td text-center text-slate-400">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2 class="mb-3 font-semibold">Perangkat</h2>
            <ul class="space-y-3">
                @forelse ($devices as $d)
                    <li class="flex items-center justify-between text-sm">
                        <div>
                            <div class="font-medium">{{ $d->name }}</div>
                            <div class="text-xs text-slate-500">{{ $d->last_seen_at?->diffForHumans() ?? 'belum pernah terhubung' }}</div>
                        </div>
                        <span class="badge {{ $d->is_online ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">{{ $d->is_online ? 'Online' : 'Offline' }}</span>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">Belum ada perangkat.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
