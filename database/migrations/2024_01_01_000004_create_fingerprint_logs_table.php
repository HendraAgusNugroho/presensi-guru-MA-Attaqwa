<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_logs', function (Blueprint $table) {
            $table->id();
            $table->string('id_fingerprint', 20);
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->onDelete('set null');
            $table->dateTime('waktu_scan');
            $table->enum('tipe', ['masuk', 'pulang'])->default('masuk');
            $table->boolean('diproses')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_logs');
    }
};
