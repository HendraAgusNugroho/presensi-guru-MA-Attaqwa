<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jadwal')->default('Jadwal Utama');
            $table->time('jam_masuk')->default('07:00:00');
            $table->time('batas_toleransi')->default('07:15:00');
            $table->time('jam_pulang')->default('15:00:00');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_masuk');
    }
};
