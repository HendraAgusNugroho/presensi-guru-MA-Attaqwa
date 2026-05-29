<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE presensis MODIFY COLUMN metode ENUM('barcode','fingerprint','manual','sistem') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensis MODIFY COLUMN metode ENUM('barcode','fingerprint','manual') NOT NULL DEFAULT 'manual'");
    }
};
