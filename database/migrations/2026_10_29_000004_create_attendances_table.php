<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rfid_uid')->index();
            $table->dateTime('scanned_at');
            $table->date('work_date')->index();
            $table->string('type')->default('in');      // in | out | unknown
            $table->string('status')->default('normal'); // on_time | late | early_leave | normal | unknown
            $table->timestamps();

            $table->index(['employee_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
