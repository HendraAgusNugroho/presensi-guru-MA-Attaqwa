<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            if (!Schema::hasColumn('presensis', 'bukti_file')) {
                $table->string('bukti_file')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('presensis', 'approval_status')) {
                $table->enum('approval_status', ['menunggu', 'disetujui', 'ditolak'])
                      ->nullable()
                      ->after('bukti_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['bukti_file', 'approval_status']);
        });
    }
};
