<form wire:submit="login" class="card w-full max-w-sm space-y-4">
    <h1 class="text-xl font-bold">Absensi RFID</h1>
    <p class="text-sm text-slate-500">Masuk untuk mengelola web service absensi.</p>

    <div>
        <label class="label">Email</label>
        <input type="email" wire:model="email" class="input" autofocus>
        @error('email') <p class="err">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label">Password</label>
        <input type="password" wire:model="password" class="input">
        @error('password') <p class="err">{{ $message }}</p> @enderror
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="remember"> Ingat saya</label>
    <button class="btn btn-primary w-full">Masuk</button>
</form>
