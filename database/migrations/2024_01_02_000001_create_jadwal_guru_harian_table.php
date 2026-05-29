<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_guru_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->tinyInteger('hari')->comment('1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat');
            $table->time('jam_masuk')->nullable()->comment('NULL = guru tidak mengajar hari ini');
            $table->time('jam_pulang')->default('14:30:00');
            $table->timestamps();

            $table->unique(['guru_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_guru_harian');
    }
};
