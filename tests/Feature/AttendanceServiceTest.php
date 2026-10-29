<?php

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns LCD lines no longer than 16 characters for a recorded attendance', function () {
    $device = Device::create([
        'name' => 'Gerbang',
        'api_key' => 'device-key',
        'is_active' => true,
    ]);
    $employee = Employee::create([
        'name' => 'Nama Pegawai Yang Sangat Panjang',
        'rfid_uid' => '04A1B2C3',
        'is_active' => true,
    ]);

    $result = app(AttendanceService::class)->record(
        $device,
        $employee->rfid_uid,
        Carbon::parse('2026-10-29 07:55:12'),
    );

    expect($result['lcd'])->toBe([
        'line1' => 'ABSEN MASUK',
        'line2' => 'NAMA PEGAWAI YAN',
    ]);
    expect(mb_strlen($result['lcd']['line1']))->toBeLessThanOrEqual(16);
    expect(mb_strlen($result['lcd']['line2']))->toBeLessThanOrEqual(16);
    expect(Attendance::count())->toBe(1);
});

it('returns LCD lines no longer than 16 characters for an unregistered card', function () {
    $device = Device::create([
        'name' => 'Gerbang',
        'api_key' => 'device-key',
        'is_active' => true,
    ]);

    $result = app(AttendanceService::class)->record(
        $device,
        '04A1B2C3',
        Carbon::parse('2026-10-29 07:55:12'),
    );

    expect($result['lcd'])->toBe([
        'line1' => 'KARTU TIDAK',
        'line2' => 'TERDAFTAR',
    ]);
    expect(mb_strlen($result['lcd']['line1']))->toBeLessThanOrEqual(16);
    expect(mb_strlen($result['lcd']['line2']))->toBeLessThanOrEqual(16);
});
