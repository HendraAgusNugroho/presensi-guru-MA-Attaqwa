<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gunakan DB::statement agar kompatibel dengan MySQL 5.7
     * renameColumn() butuh Doctrine DBAL yang sering tidak ada di shared hosting.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'nip')) {
            DB::statement('ALTER TABLE `users` CHANGE `nip` `id_pengguna` VARCHAR(30) NOT NULL');
        }

        if (Schema::hasColumn('gurus', 'nip')) {
            DB::statement('ALTER TABLE `gurus` CHANGE `nip` `id_pengguna` VARCHAR(30) NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'id_pengguna')) {
            DB::statement('ALTER TABLE `users` CHANGE `id_pengguna` `nip` VARCHAR(30) NOT NULL');
        }

        if (Schema::hasColumn('gurus', 'id_pengguna')) {
            DB::statement('ALTER TABLE `gurus` CHANGE `id_pengguna` `nip` VARCHAR(30) NOT NULL');
        }
    }
};
