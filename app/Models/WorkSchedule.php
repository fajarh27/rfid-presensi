<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time', 'late_tolerance', 'work_days'];

    protected function casts(): array
    {
        return ['work_days' => 'array'];
    }

    public const DAYS = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function getDaysLabelAttribute(): string
    {
        return collect($this->work_days ?? [])->map(fn ($d) => substr(self::DAYS[(int) $d] ?? '', 0, 3))->implode(', ');
    }
}
