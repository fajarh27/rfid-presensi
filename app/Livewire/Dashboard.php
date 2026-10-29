<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $today = today()->toDateString();

        return view('livewire.dashboard', [
            'total'   => Employee::where('is_active', true)->count(),
            'present' => Attendance::where('work_date', $today)->whereNotNull('employee_id')->distinct()->count('employee_id'),
            'late'    => Attendance::where('work_date', $today)->where('status', 'late')->count(),
            'unknown' => Attendance::where('work_date', $today)->whereNull('employee_id')->count(),
            'devices' => Device::orderBy('name')->get(),
            'latest'  => Attendance::with(['employee', 'device'])->latest('scanned_at')->limit(10)->get(),
        ]);
    }
}
