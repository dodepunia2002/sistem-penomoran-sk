<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix Pengajuan table
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->date('tanggal')->change();
        });

        // 2. Fix Riwayat table
        Schema::table('riwayat', function (Blueprint $table) {
            $table->date('tanggal')->change();
            $table->string('nomor_sk', 100)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->string('tanggal')->change();
        });

        Schema::table('riwayat', function (Blueprint $table) {
            $table->string('tanggal')->change();
            $table->dropUnique(['nomor_sk']);
        });
    }
};
