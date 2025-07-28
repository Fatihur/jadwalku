<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materi;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class MateriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus materi lama
        Materi::truncate();

        // Ambil data yang diperlukan
        $gurus = Guru::all();
        $mataPelajarans = MataPelajaran::all();
        $kelas = Kelas::all();

        if ($gurus->isEmpty() || $mataPelajarans->isEmpty()) {
            $this->command->error('Data guru dan mata pelajaran harus ada terlebih dahulu.');
            return;
        }

        $materiData = [
            [
                'judul_materi' => 'Pengenalan Aljabar Dasar',
                'deskripsi' => 'Materi pengenalan konsep dasar aljabar untuk siswa kelas 7',
                'tipe_materi' => 'dokumen',
                'is_published' => true,
            ],
            [
                'judul_materi' => 'Sistem Persamaan Linear',
                'deskripsi' => 'Pembahasan lengkap tentang sistem persamaan linear dua variabel',
                'tipe_materi' => 'presentasi',
                'is_published' => true,
            ],
            [
                'judul_materi' => 'Video Tutorial Geometri',
                'deskripsi' => 'Video pembelajaran interaktif tentang bangun datar dan ruang',
                'tipe_materi' => 'video',
                'is_published' => false,
            ],
            [
                'judul_materi' => 'Latihan Soal Trigonometri',
                'deskripsi' => 'Kumpulan soal latihan trigonometri dengan pembahasan',
                'tipe_materi' => 'dokumen',
                'is_published' => true,
            ],
            [
                'judul_materi' => 'Materi Bahasa Indonesia Kelas 8',
                'deskripsi' => 'Materi pembelajaran bahasa Indonesia untuk kelas 8',
                'tipe_materi' => 'dokumen',
                'is_published' => true,
            ],
            [
                'judul_materi' => 'Grammar Bahasa Inggris',
                'deskripsi' => 'Materi tata bahasa Inggris dasar untuk pemula',
                'tipe_materi' => 'presentasi',
                'is_published' => true,
            ],
        ];

        $materiCount = 0;

        foreach ($materiData as $data) {
            $guru = $gurus->random();
            $mataPelajaran = $mataPelajarans->random();
            $kelasTarget = $kelas->random();

            Materi::create([
                'guru_id' => $guru->id,
                'mata_pelajaran_id' => $mataPelajaran->id,
                'kelas_id' => rand(0, 1) ? $kelasTarget->id : null, // 50% chance untuk kelas spesifik
                'judul_materi' => $data['judul_materi'],
                'deskripsi' => $data['deskripsi'],
                'tipe_materi' => $data['tipe_materi'],
                'is_published' => $data['is_published'],
            ]);

            $materiCount++;
        }

        $this->command->info("Berhasil membuat {$materiCount} materi pembelajaran");
    }
}
