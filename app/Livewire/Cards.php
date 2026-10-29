<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kartu RFID')]
class Cards extends Component
{
    public ?string $assignUid = null;

    public $employee_id = '';

    public string $new_name = '';

    public $work_schedule_id = '';

    public function assign(string $uid): void
    {
        $this->resetValidation();
        $this->assignUid = $uid;
        $this->employee_id = '';
        $this->new_name = '';
        $this->work_schedule_id = WorkSchedule::value('id') ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'new_name' => 'required_without:employee_id|nullable|string|max:100',
        ], [
            'new_name.required_without' => 'Pilih peserta atau isi nama baru.',
        ]);

        if (Employee::where('rfid_uid', $this->assignUid)->exists()) {
            $this->addError('employee_id', 'UID ini sudah terdaftar.');

            return;
        }

        if ($this->employee_id) {
            Employee::findOrFail($this->employee_id)->update(['rfid_uid' => $this->assignUid]);
        } else {
            Employee::create([
                'name' => $this->new_name,
                'rfid_uid' => $this->assignUid,
                'work_schedule_id' => $this->work_schedule_id ?: null,
            ]);
        }

        $this->assignUid = null;
    }

    public function discard(string $uid): void
    {
        Attendance::whereNull('employee_id')->where('rfid_uid', $uid)->delete();
    }

    public function render()
    {
        $unregistered = Attendance::whereNull('employee_id')
            ->whereNotIn('rfid_uid', Employee::whereNotNull('rfid_uid')->select('rfid_uid'))
            ->select('rfid_uid', DB::raw('count(*) as total'), DB::raw('max(scanned_at) as last_scan'))
            ->groupBy('rfid_uid')
            ->orderByDesc('last_scan')
            ->get();

        return view('livewire.cards', [
            'unregistered' => $unregistered,
            'freeEmployees' => Employee::whereNull('rfid_uid')->orderBy('name')->get(),
            'schedules' => WorkSchedule::orderBy('name')->get(),
        ]);
    }
}
