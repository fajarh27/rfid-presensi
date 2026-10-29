<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Data Absensi')]
class Attendances extends Component
{
    use WithPagination;

    public string $date_from = '';
    public string $date_to = '';
    public $employee_id = '';
    public string $status = '';

    public function mount(): void
    {
        $this->date_from = $this->date_to = today()->toDateString();
    }

    public function updated($name): void
    {
        if (in_array($name, ['date_from', 'date_to', 'employee_id', 'status'])) {
            $this->resetPage();
        }
    }

    protected function query()
    {
        return Attendance::with(['employee', 'device'])
            ->when($this->date_from, fn ($q) => $q->where('work_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->where('work_date', '<=', $this->date_to))
            ->when($this->employee_id, fn ($q) => $q->where('employee_id', $this->employee_id))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('scanned_at');
    }

    public function delete(int $id): void
    {
        Attendance::findOrFail($id)->delete();
    }

    public function export()
    {
        $rows = $this->query()->get();

        return response()->streamDownload(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Tanggal', 'Waktu', 'Nama', 'NIP', 'UID', 'Jenis', 'Status', 'Perangkat']);
            foreach ($rows as $r) {
                fputcsv($f, [
                    $r->work_date->format('Y-m-d'),
                    $r->scanned_at->format('H:i:s'),
                    $r->employee?->name ?? '(tidak dikenal)',
                    $r->employee?->nip,
                    $r->rfid_uid,
                    $r->type_label,
                    $r->status_label,
                    $r->device?->name,
                ]);
            }
            fclose($f);
        }, 'absensi-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.attendances', [
            'rows' => $this->query()->paginate(15),
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'statuses' => Attendance::STATUS,
        ]);
    }
}
