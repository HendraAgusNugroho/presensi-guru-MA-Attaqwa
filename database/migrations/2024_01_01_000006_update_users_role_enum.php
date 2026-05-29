<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing roles ke struktur baru sebelum alter enum
        DB::statement("UPDATE users SET role = 'super_admin' WHERE role IN ('kepala_sekolah')");
        DB::statement("UPDATE users SET role = 'admin' WHERE role IN ('admin', 'wakil_kepsek')");

        // Alter enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','guru') NOT NULL DEFAULT 'guru'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kepala_sekolah','wakil_kepsek','guru') NOT NULL DEFAULT 'guru'");
    }
};
