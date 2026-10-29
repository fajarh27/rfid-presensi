<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('Password123#'),
        ]);

        WorkSchedule::firstOrCreate(['name' => 'Jam Kerja Reguler'], [
            'start_time' => '08:00',
            'end_time' => '16:00',
            'late_tolerance' => 10,
            'work_days' => [1, 2, 3, 4, 5],
        ]);

        Device::firstOrCreate(['name' => 'Gerbang Utama'], [
            'location' => 'Pintu masuk',
            'api_key' => Str::random(40),
        ]);
    }
}
