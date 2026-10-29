<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['employee_id', 'device_id', 'rfid_uid', 'scanned_at', 'work_date', 'type', 'status'];

    protected function casts(): array
    {
        return ['scanned_at' => 'datetime', 'work_date' => 'date'];
    }

    public const STATUS = [
        'on_time'     => ['Tepat waktu', 'bg-green-100 text-green-700'],
        'late'        => ['Terlambat', 'bg-amber-100 text-amber-700'],
        'early_leave' => ['Pulang cepat', 'bg-orange-100 text-orange-700'],
        'normal'      => ['Normal', 'bg-slate-100 text-slate-600'],
        'unknown'     => ['Kartu tidak dikenal', 'bg-red-100 text-red-700'],
    ];

    public const TYPE = ['in' => 'Masuk', 'out' => 'Pulang', 'unknown' => '-'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status][0] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS[$this->status][1] ?? 'bg-slate-100 text-slate-600';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE[$this->type] ?? $this->type;
    }
}
