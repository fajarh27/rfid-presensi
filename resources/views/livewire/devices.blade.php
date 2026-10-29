<div class="space-y-4">
    <div class="card space-y-2 text-sm">
        <div class="font-semibold">Cara menghubungkan perangkat RFID</div>
        <p class="text-slate-600">Kirim <code>POST</code> ke endpoint berikut dengan header <code>X-Device-Key</code> berisi API key perangkat:</p>
        <pre class="overflow-x-auto rounded-lg bg-slate-900 p-3 text-xs text-slate-100">curl -X POST {{ $endpoint }} \
  -H "X-Device-Key: API_KEY_PERANGKAT" \
  -H "Accept: application/json" \
  -d "uid=04A1B2C3"</pre>
    </div>

    <div class="flex justify-end"><button wire:click="create" class="btn btn-primary">+ Tambah perangkat</button></div>

    <div class="space-y-3">
        @forelse ($devices as $d)
            <div class="card" wire:key="dev-{{ $d->id }}" x-data="{ show: false }">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="font-semibold">{{ $d->name }}
                            <span class="badge ml-2 {{ $d->is_active ? ($d->is_online ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500') : 'bg-red-100 text-red-700' }}">
                                {{ $d->is_active ? ($d->is_online ? 'Online' : 'Offline') : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500">{{ $d->location ?? '-' }} · terakhir terlihat {{ $d->last_seen_at?->diffForHumans() ?? 'belum pernah' }}</div>
                    </div>
                    <div class="space-x-1">
                        <button wire:click="toggle({{ $d->id }})" class="btn btn-ghost">{{ $d->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        <button wire:click="edit({{ $d->id }})" class="btn btn-ghost">Ubah</button>
                        <button wire:click="delete({{ $d->id }})" wire:confirm="Hapus perangkat ini?" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-slate-500">API key:</span>
                    <code class="rounded bg-slate-100 px-2 py-1 text-xs" x-text="show ? '{{ $d->api_key }}' : '••••••••••••••••••••'"></code>
                    <button type="button" class="text-xs text-indigo-600 hover:underline" x-on:click="show = !show" x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
                    <button type="button" class="text-xs text-indigo-600 hover:underline" x-on:click="navigator.clipboard.writeText('{{ $d->api_key }}')">Salin</button>
                    <button type="button" wire:click="regenerate({{ $d->id }})" wire:confirm="Buat API key baru? Key lama langsung tidak berlaku." class="text-xs text-red-600 hover:underline">Buat ulang</button>
                </div>
            </div>
        @empty
            <div class="card text-center text-sm text-slate-400">Belum ada perangkat.</div>
        @endforelse
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-10 flex items-center justify-center bg-black/40 p-4">
            <form wire:submit="save" class="card w-full max-w-md space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Ubah perangkat' : 'Tambah perangkat' }}</h2>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="err">{{ $message }}</p>@enderror</div>
                <div><label class="label">Lokasi</label><input wire:model="location" class="input">@error('location')<p class="err">{{ $message }}</p>@enderror</div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-ghost">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    @endif
</div>
