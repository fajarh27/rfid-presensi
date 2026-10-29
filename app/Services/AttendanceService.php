<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceService
{
    /** Scan ganda dalam rentang detik ini diabaikan. */
    public const DEBOUNCE_SECONDS = 60;

    public function record(Device $device, string $uid, ?Carbon $at = null): array
    {
        $at = $at ?? now();
        $uid = strtoupper(trim($uid));
        $date = $at->toDateString();

        $employee = Employee::with('schedule')
            ->where('rfid_uid', $uid)
            ->where('is_active', true)
            ->first();

        // Kartu belum terdaftar: simpan supaya admin bisa memberi nama di menu "Kartu RFID".
        if (! $employee) {
            Attendance::create([
                'employee_id' => null,
                'device_id'   => $device->id,
                'rfid_uid'    => $uid,
                'scanned_at'  => $at,
                'work_date'   => $date,
                'type'        => 'unknown',
                'status'      => 'unknown',
            ]);

            return ['ok' => false, 'code' => 'unregistered', 'message' => 'Kartu belum terdaftar', 'uid' => $uid];
        }

        $last = Attendance::where('employee_id', $employee->id)
            ->where('work_date', $date)
            ->orderByDesc('scanned_at')
            ->first();

        if ($last && abs($at->timestamp - $last->scanned_at->timestamp) < self::DEBOUNCE_SECONDS) {
            return [
                'ok' => true, 'code' => 'duplicate', 'message' => 'Scan ganda diabaikan',
                'name' => $employee->name, 'type' => $last->type, 'status' => $last->status,
                'time' => $last->scanned_at->format('H:i:s'),
            ];
        }

        $type = ($last && $last->type === 'in') ? 'out' : 'in';
        $status = $this->resolveStatus($employee, $type, $at, $date);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'device_id'   => $device->id,
            'rfid_uid'    => $uid,
            'scanned_at'  => $at,
            'work_date'   => $date,
            'type'        => $type,
            'status'      => $status,
        ]);

        return [
            'ok' => true, 'code' => 'recorded',
            'message' => $type === 'in' ? 'Absen masuk berhasil' : 'Absen pulang berhasil',
            'name' => $employee->name, 'type' => $type, 'status' => $status,
            'time' => $attendance->scanned_at->format('H:i:s'),
        ];
    }

    private function resolveStatus(Employee $employee, string $type, Carbon $at, string $date): string
    {
        $schedule = $employee->schedule;

        if (! $schedule) {
            return 'normal';
        }

        if ($type === 'in') {
            $limit = Carbon::parse("$date {$schedule->start_time}")->addMinutes((int) $schedule->late_tolerance);

            return $at->gt($limit) ? 'late' : 'on_time';
        }

        $end = Carbon::parse("$date {$schedule->end_time}");

        return $at->lt($end) ? 'early_leave' : 'normal';
    }
}
