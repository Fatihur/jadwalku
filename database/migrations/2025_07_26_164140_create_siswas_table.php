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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('nisn')->unique();
            $table->string('nis')->unique();
            $table->year('tahun_masuk');
            $table->enum('status_siswa', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->string('nama_orang_tua')->nullable();
            $table->string('nomor_telepon_orang_tua')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
