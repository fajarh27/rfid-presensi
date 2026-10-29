<?php

namespace App\Livewire;

use App\Models\WorkSchedule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Jam Kerja')]
class Schedules extends Component
{
    public bool $showForm = false;
    public $editingId = null;

    public string $name = '';
    public string $start_time = '08:00';
    public string $end_time = '16:00';
    public $late_tolerance = 0;
    public array $work_days = ['1', '2', '3', '4', '5'];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_tolerance' => 'required|integer|min:0|max:240',
            'work_days' => 'required|array|min:1',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['editingId', 'name']);
        $this->start_time = '08:00';
        $this->end_time = '16:00';
        $this->late_tolerance = 0;
        $this->work_days = ['1', '2', '3', '4', '5'];
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $s = WorkSchedule::findOrFail($id);
        $this->resetValidation();
        $this->editingId = $s->id;
        $this->name = $s->name;
        $this->start_time = substr($s->start_time, 0, 5);
        $this->end_time = substr($s->end_time, 0, 5);
        $this->late_tolerance = $s->late_tolerance;
        $this->work_days = array_map('strval', $s->work_days ?? []);
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        WorkSchedule::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'late_tolerance' => (int) $this->late_tolerance,
            'work_days' => array_values(array_map('intval', $this->work_days)),
        ]);

        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        WorkSchedule::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.schedules', [
            'schedules' => WorkSchedule::withCount('employees')->orderBy('name')->get(),
            'days' => WorkSchedule::DAYS,
        ]);
    }
}
