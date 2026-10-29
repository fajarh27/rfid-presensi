<div class="space-y-4">
    <div class="flex justify-end"><button wire:click="create" class="btn btn-primary">+ Tambah jam kerja</button></div>

    <div class="card overflow-x-auto p-0">
        <table class="min-w-full divide-y divide-slate-100">
            <thead><tr><th class="th">Nama</th><th class="th">Masuk</th><th class="th">Pulang</th><th class="th">Toleransi</th><th class="th">Hari kerja</th><th class="th">Peserta</th><th class="th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($schedules as $s)
                <tr wire:key="sch-{{ $s->id }}">
                    <td class="td font-medium">{{ $s->name }}</td>
                    <td class="td">{{ substr($s->start_time, 0, 5) }}</td>
                    <td class="td">{{ substr($s->end_time, 0, 5) }}</td>
                    <td class="td">{{ $s->late_tolerance }} menit</td>
                    <td class="td">{{ $s->days_label }}</td>
                    <td class="td">{{ $s->employees_count }}</td>
                    <td class="td space-x-1 whitespace-nowrap text-right">
                        <button wire:click="edit({{ $s->id }})" class="btn btn-ghost">Ubah</button>
                        <button wire:click="delete({{ $s->id }})" wire:confirm="Hapus jam kerja ini? Peserta terkait menjadi tanpa jadwal." class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="td text-center text-slate-400">Belum ada jam kerja.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-10 flex items-center justify-center bg-black/40 p-4">
            <form wire:submit="save" class="card w-full max-w-md space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Ubah jam kerja' : 'Tambah jam kerja' }}</h2>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="err">{{ $message }}</p>@enderror</div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Jam masuk</label><input type="time" wire:model="start_time" class="input">@error('start_time')<p class="err">{{ $message }}</p>@enderror</div>
                    <div><label class="label">Jam pulang</label><input type="time" wire:model="end_time" class="input">@error('end_time')<p class="err">{{ $message }}</p>@enderror</div>
                </div>
                <div><label class="label">Toleransi terlambat (menit)</label><input type="number" min="0" wire:model="late_tolerance" class="input">@error('late_tolerance')<p class="err">{{ $message }}</p>@enderror</div>
                <div>
                    <label class="label">Hari kerja</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($days as $n => $label)
                            <label class="flex items-center gap-1 text-sm"><input type="checkbox" wire:model="work_days" value="{{ $n }}"> {{ $label }}</label>
                        @endforeach
                    </div>
                    @error('work_days')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-ghost">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    @endif
</div>
