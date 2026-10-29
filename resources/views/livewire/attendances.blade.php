<div class="space-y-4">
    <div class="card grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
        <div><label class="label">Dari</label><input type="date" wire:model.live="date_from" class="input"></div>
        <div><label class="label">Sampai</label><input type="date" wire:model.live="date_to" class="input"></div>
        <div>
            <label class="label">Peserta</label>
            <select wire:model.live="employee_id" class="input">
                <option value="">Semua</option>
                @foreach ($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label">Status</label>
            <select wire:model.live="status" class="input">
                <option value="">Semua</option>
                @foreach ($statuses as $key => [$label])<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <button wire:click="export" class="btn btn-ghost">Unduh CSV</button>
    </div>

    <div class="card overflow-x-auto p-0">
        <table class="min-w-full divide-y divide-slate-100">
            <thead><tr><th class="th">Tanggal</th><th class="th">Waktu</th><th class="th">Nama</th><th class="th">UID</th><th class="th">Jenis</th><th class="th">Status</th><th class="th">Perangkat</th><th class="th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($rows as $r)
                <tr wire:key="att-{{ $r->id }}">
                    <td class="td whitespace-nowrap">{{ $r->work_date->format('d/m/Y') }}</td>
                    <td class="td">{{ $r->scanned_at->format('H:i:s') }}</td>
                    <td class="td">{{ $r->employee?->name ?? '—' }}</td>
                    <td class="td font-mono text-xs">{{ $r->rfid_uid }}</td>
                    <td class="td">{{ $r->type_label }}</td>
                    <td class="td"><span class="badge {{ $r->status_class }}">{{ $r->status_label }}</span></td>
                    <td class="td">{{ $r->device?->name ?? '-' }}</td>
                    <td class="td text-right"><button wire:click="delete({{ $r->id }})" wire:confirm="Hapus catatan ini?" class="text-xs text-red-600 hover:underline">Hapus</button></td>
                </tr>
            @empty
                <tr><td colspan="8" class="td text-center text-slate-400">Tidak ada data pada filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
