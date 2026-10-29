<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\WorkSchedule;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Peserta')]
class Employees extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public $editingId = null;

    public string $name = '';

    public $nip = '';

    public $rfid_uid = '';

    public $work_schedule_id = '';

    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('employees', 'nip')->ignore($this->editingId)],
            'rfid_uid' => ['nullable', 'string', 'max:64', Rule::unique('employees', 'rfid_uid')->ignore($this->editingId)],
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'is_active' => 'boolean',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['editingId', 'name', 'nip', 'rfid_uid', 'work_schedule_id']);
        $this->is_active = true;
        $this->work_schedule_id = WorkSchedule::value('id') ?? '';
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $e = Employee::findOrFail($id);
        $this->resetValidation();
        $this->editingId = $e->id;
        $this->name = $e->name;
        $this->nip = $e->nip ?? '';
        $this->rfid_uid = $e->rfid_uid ?? '';
        $this->work_schedule_id = $e->work_schedule_id ?? '';
        $this->is_active = $e->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->rfid_uid = strtoupper(trim((string) $this->rfid_uid));
        $this->validate();

        Employee::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'nip' => $this->nip ?: null,
            'rfid_uid' => $this->rfid_uid ?: null,
            'work_schedule_id' => $this->work_schedule_id ?: null,
            'is_active' => $this->is_active,
        ]);

        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Employee::findOrFail($id)->delete();
    }

    public function render()
    {
        $employees = Employee::with('schedule')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('nip', 'like', "%{$this->search}%")
                    ->orWhere('rfid_uid', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.employees', [
            'employees' => $employees,
            'schedules' => WorkSchedule::orderBy('name')->get(),
        ]);
    }
}
