<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP / UID…" class="input max-w-xs">
        <button wire:click="create" class="btn btn-primary">+ Tambah peserta</button>
    </div>

    <div class="card overflow-x-auto p-0">
        <table class="min-w-full divide-y divide-slate-100">
            <thead><tr><th class="th">Nama</th><th class="th">NIP</th><th class="th">UID RFID</th><th class="th">Jam kerja</th><th class="th">Status</th><th class="th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($employees as $e)
                <tr wire:key="emp-{{ $e->id }}">
                    <td class="td font-medium">{{ $e->name }}</td>
                    <td class="td">{{ $e->nip ?? '-' }}</td>
                    <td class="td font-mono text-xs">{{ $e->rfid_uid ?? '-' }}</td>
                    <td class="td">{{ $e->schedule?->name ?? '-' }}</td>
                    <td class="td"><span class="badge {{ $e->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">{{ $e->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="td space-x-1 whitespace-nowrap text-right">
                        <button wire:click="edit({{ $e->id }})" class="btn btn-ghost">Ubah</button>
                        <button wire:click="delete({{ $e->id }})" wire:confirm="Hapus peserta ini?" class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="td text-center text-slate-400">Belum ada peserta.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $employees->links() }}

    @if ($showForm)
        <div class="fixed inset-0 z-10 flex items-center justify-center bg-black/40 p-4">
            <form wire:submit="save" class="card w-full max-w-md space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Ubah peserta' : 'Tambah peserta' }}</h2>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="err">{{ $message }}</p>@enderror</div>
                <div><label class="label">NIP</label><input wire:model="nip" class="input">@error('nip')<p class="err">{{ $message }}</p>@enderror</div>
                <div><label class="label">UID RFID</label><input wire:model="rfid_uid" class="input font-mono" placeholder="mis. 04A1B2C3">@error('rfid_uid')<p class="err">{{ $message }}</p>@enderror</div>
                <div>
                    <label class="label">Jam kerja</label>
                    <select wire:model="work_schedule_id" class="input">
                        <option value="">— tanpa jadwal —</option>
                        @foreach ($schedules as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time,0,5) }}–{{ substr($s->end_time,0,5) }})</option>@endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active"> Aktif</label>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-ghost">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    @endif
</div>
