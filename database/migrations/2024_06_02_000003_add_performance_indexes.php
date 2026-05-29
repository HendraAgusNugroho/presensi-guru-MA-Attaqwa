<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index pada kolom yang sering dipakai untuk filter/sort
     * agar performa query di dashboard & laporan lebih cepat.
     */
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Dashboard query: whereDate('tanggal', ...) dan whereBetween('tanggal', ...)
            if (!$this->indexExists('presensis', 'idx_presensis_tanggal')) {
                $table->index('tanggal', 'idx_presensis_tanggal');
            }
            // Filter tanggal + status sekaligus (grafik dashboard)
            if (!$this->indexExists('presensis', 'idx_presensis_tanggal_status')) {
                $table->index(['tanggal', 'status'], 'idx_presensis_tanggal_status');
            }
        });

        Schema::table('gurus', function (Blueprint $table) {
            // Barcode scan: Guru::where('barcode', ...) — kolom sudah unique
            // tapi eksplisit index nama custom membantu explain plan
            if (!$this->indexExists('gurus', 'idx_gurus_barcode')) {
                $table->index('barcode', 'idx_gurus_barcode');
            }
            // Fingerprint sync: Guru::where('id_fingerprint', ...)
            if (!$this->indexExists('gurus', 'idx_gurus_id_fingerprint')) {
                $table->index('id_fingerprint', 'idx_gurus_id_fingerprint');
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropIndex('idx_presensis_tanggal');
            $table->dropIndex('idx_presensis_tanggal_status');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropIndex('idx_gurus_barcode');
            $table->dropIndex('idx_gurus_id_fingerprint');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
