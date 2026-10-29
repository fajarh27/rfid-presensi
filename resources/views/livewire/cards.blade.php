<div wire:poll.10s class="space-y-4">
    <p class="text-sm text-slate-600">Kartu yang pernah di-tap tetapi belum terdaftar muncul di sini. Beri nama dengan menautkannya ke peserta.</p>

    <div class="card overflow-x-auto p-0">
        <table class="min-w-full divide-y divide-slate-100">
            <thead><tr><th class="th">UID</th><th class="th">Jumlah scan</th><th class="th">Terakhir</th><th class="th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($unregistered as $c)
                <tr wire:key="card-{{ $c->rfid_uid }}">
                    <td class="td font-mono">{{ $c->rfid_uid }}</td>
                    <td class="td">{{ $c->total }}</td>
                    <td class="td">{{ \Illuminate\Support\Carbon::parse($c->last_scan)->format('d/m/Y H:i:s') }}</td>
                    <td class="td space-x-1 whitespace-nowrap text-right">
                        <button wire:click="assign('{{ $c->rfid_uid }}')" class="btn btn-primary">Beri nama</button>
                        <button wire:click="discard('{{ $c->rfid_uid }}')" wire:confirm="Hapus log scan UID ini?" class="btn btn-danger">Abaikan</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="td text-center text-slate-400">Tidak ada kartu baru.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($assignUid)
        <div class="fixed inset-0 z-10 flex items-center justify-center bg-black/40 p-4">
            <form wire:submit="save" class="card w-full max-w-md space-y-4">
                <h2 class="text-lg font-semibold">Daftarkan kartu <span class="font-mono">{{ $assignUid }}</span></h2>

                <div>
                    <label class="label">Tautkan ke peserta yang belum punya kartu</label>
                    <select wire:model="employee_id" class="input">
                        <option value="">— atau buat peserta baru —</option>
                        @foreach ($freeEmployees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
                    </select>
                    @error('employee_id')<p class="err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Nama peserta baru</label>
                    <input wire:model="new_name" class="input" placeholder="Isi jika membuat peserta baru">
                    @error('new_name')<p class="err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Jam kerja (untuk peserta baru)</label>
                    <select wire:model="work_schedule_id" class="input">
                        <option value="">— tanpa jadwal —</option>
                        @foreach ($schedules as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('assignUid', null)" class="btn btn-ghost">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    @endif
</div>
