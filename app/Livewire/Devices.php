<?php

namespace App\Livewire;

use App\Models\Device;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Perangkat')]
class Devices extends Component
{
    public bool $showForm = false;
    public $editingId = null;
    public string $name = '';
    public string $location = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:150',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['editingId', 'name', 'location']);
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $d = Device::findOrFail($id);
        $this->resetValidation();
        $this->editingId = $d->id;
        $this->name = $d->name;
        $this->location = $d->location ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Device::findOrFail($this->editingId)->update(['name' => $this->name, 'location' => $this->location ?: null]);
        } else {
            Device::create([
                'name' => $this->name,
                'location' => $this->location ?: null,
                'api_key' => Str::random(40),
            ]);
        }

        $this->showForm = false;
    }

    public function toggle(int $id): void
    {
        $d = Device::findOrFail($id);
        $d->update(['is_active' => ! $d->is_active]);
    }

    public function regenerate(int $id): void
    {
        Device::findOrFail($id)->update(['api_key' => Str::random(40)]);
    }

    public function delete(int $id): void
    {
        Device::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.devices', [
            'devices' => Device::orderBy('name')->get(),
            'endpoint' => url('/api/v1/attendance'),
        ]);
    }
}
