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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan');
            $table->string('kode_ruangan')->unique();
            $table->integer('kapasitas')->default(30);
            $table->enum('tipe_ruangan', ['kelas', 'laboratorium', 'perpustakaan', 'aula', 'olahraga'])->default('kelas');
            $table->json('fasilitas')->nullable(); // Array fasilitas yang tersedia
            $table->string('lokasi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
