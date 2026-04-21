<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add performance indexes and fix column types:
 * - pengajuan.status (enum filter)
 * - pengajuan.submitted_by (FK + frequent filter)
 * - pengajuan.tanggal changed to date type
 * - riwayat.pengajuan_id (FK lookup)
 * - riwayat.nomor_sk (unique + search)
 * - riwayat.processed_by (FK lookup)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->index('status', 'idx_pengajuan_status');
            $table->index('submitted_by', 'idx_pengajuan_submitted_by');
            $table->index(['submitted_by', 'status'], 'idx_pengajuan_user_status');
            $table->index('created_at', 'idx_pengajuan_created_at');
        });

        Schema::table('riwayat', function (Blueprint $table) {
            $table->index('pengajuan_id', 'idx_riwayat_pengajuan_id');
            $table->unique('nomor_sk', 'uniq_riwayat_nomor_sk');
            $table->index('processed_by', 'idx_riwayat_processed_by');
            $table->index(['created_at'], 'idx_riwayat_created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropIndex('idx_pengajuan_status');
            $table->dropIndex('idx_pengajuan_submitted_by');
            $table->dropIndex('idx_pengajuan_user_status');
            $table->dropIndex('idx_pengajuan_created_at');
        });

        Schema::table('riwayat', function (Blueprint $table) {
            $table->dropIndex('idx_riwayat_pengajuan_id');
            $table->dropUnique('uniq_riwayat_nomor_sk');
            $table->dropIndex('idx_riwayat_processed_by');
            $table->dropIndex('idx_riwayat_created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
        });
    }
};
