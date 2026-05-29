<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('id_pengguna', 30)->unique();
            $table->string('nama');
            $table->string('email')->unique()->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('jabatan')->nullable();
            $table->string('mata_pelajaran')->nullable();
            $table->string('id_fingerprint', 20)->nullable()->unique();
            $table->string('barcode')->unique();
            $table->string('foto')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
