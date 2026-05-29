<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['hadir', 'telat', 'tidak_hadir', 'izin', 'sakit'])->default('tidak_hadir');
            $table->enum('metode', ['barcode', 'fingerprint', 'manual'])->default('manual');
            $table->integer('menit_telat')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['guru_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
