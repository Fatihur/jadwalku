<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;
use App\Models\Ruangan;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Mata Pelajaran
        $mataPelajaran = [
            [
                'nama_mata_pelajaran' => 'Matematika',
                'kode_mata_pelajaran' => 'MAT',
                'deskripsi' => 'Mata pelajaran matematika untuk semua tingkat',
                'jam_per_minggu' => 4,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
            [
                'nama_mata_pelajaran' => 'Bahasa Indonesia',
                'kode_mata_pelajaran' => 'BIN',
                'deskripsi' => 'Mata pelajaran bahasa Indonesia',
                'jam_per_minggu' => 3,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
            [
                'nama_mata_pelajaran' => 'Bahasa Inggris',
                'kode_mata_pelajaran' => 'ENG',
                'deskripsi' => 'Mata pelajaran bahasa Inggris',
                'jam_per_minggu' => 3,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
            [
                'nama_mata_pelajaran' => 'Fisika',
                'kode_mata_pelajaran' => 'FIS',
                'deskripsi' => 'Mata pelajaran fisika untuk jurusan IPA',
                'jam_per_minggu' => 3,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
            [
                'nama_mata_pelajaran' => 'Kimia',
                'kode_mata_pelajaran' => 'KIM',
                'deskripsi' => 'Mata pelajaran kimia untuk jurusan IPA',
                'jam_per_minggu' => 3,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
            [
                'nama_mata_pelajaran' => 'Biologi',
                'kode_mata_pelajaran' => 'BIO',
                'deskripsi' => 'Mata pelajaran biologi untuk jurusan IPA',
                'jam_per_minggu' => 3,
                'tingkat' => ['10', '11', '12'],
                'is_active' => true,
            ],
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::create([
                'nama_mata_pelajaran' => $mapel['nama_mata_pelajaran'],
                'kode_mata_pelajaran' => $mapel['kode_mata_pelajaran'],
                'deskripsi' => $mapel['deskripsi'],
                'jam_per_minggu' => $mapel['jam_per_minggu'],
                'tingkat' => json_encode($mapel['tingkat']),
                'is_active' => $mapel['is_active'],
            ]);
        }

        // Create Ruangan
        $ruangan = [
            [
                'nama_ruangan' => 'Ruang Kelas 10A',
                'kode_ruangan' => 'R10A',
                'kapasitas' => 30,
                'tipe_ruangan' => 'kelas',
                'fasilitas' => ['proyektor', 'ac', 'papan_tulis'],
                'lokasi' => 'Lantai 1',
                'is_active' => true,
            ],
            [
                'nama_ruangan' => 'Ruang Kelas 10B',
                'kode_ruangan' => 'R10B',
                'kapasitas' => 30,
                'tipe_ruangan' => 'kelas',
                'fasilitas' => ['proyektor', 'ac', 'papan_tulis'],
                'lokasi' => 'Lantai 1',
                'is_active' => true,
            ],
            [
                'nama_ruangan' => 'Laboratorium Fisika',
                'kode_ruangan' => 'LAB_FIS',
                'kapasitas' => 25,
                'tipe_ruangan' => 'laboratorium',
                'fasilitas' => ['alat_praktikum', 'proyektor', 'ac'],
                'lokasi' => 'Lantai 2',
                'is_active' => true,
            ],
            [
                'nama_ruangan' => 'Laboratorium Kimia',
                'kode_ruangan' => 'LAB_KIM',
                'kapasitas' => 25,
                'tipe_ruangan' => 'laboratorium',
                'fasilitas' => ['alat_praktikum', 'proyektor', 'ac', 'fume_hood'],
                'lokasi' => 'Lantai 2',
                'is_active' => true,
            ],
            [
                'nama_ruangan' => 'Laboratorium Biologi',
                'kode_ruangan' => 'LAB_BIO',
                'kapasitas' => 25,
                'tipe_ruangan' => 'laboratorium',
                'fasilitas' => ['mikroskop', 'proyektor', 'ac'],
                'lokasi' => 'Lantai 2',
                'is_active' => true,
            ],
        ];

        foreach ($ruangan as $room) {
            Ruangan::create([
                'nama_ruangan' => $room['nama_ruangan'],
                'kode_ruangan' => $room['kode_ruangan'],
                'kapasitas' => $room['kapasitas'],
                'tipe_ruangan' => $room['tipe_ruangan'],
                'fasilitas' => json_encode($room['fasilitas']),
                'lokasi' => $room['lokasi'],
                'is_active' => $room['is_active'],
            ]);
        }

        // Create additional teachers
        $teachers = [
            [
                'nama' => 'Dr. Ahmad Wijaya',
                'email' => 'ahmad.wijaya@edugenis.com',
                'nip' => '198501012010011001',
                'bidang_keahlian' => 'Matematika',
                'mata_pelajaran' => ['MAT'],
            ],
            [
                'nama' => 'Sari Indrawati, S.Pd',
                'email' => 'sari.indrawati@edugenis.com',
                'nip' => '198703152012012002',
                'bidang_keahlian' => 'Bahasa Indonesia',
                'mata_pelajaran' => ['BIN'],
            ],
            [
                'nama' => 'John Smith, M.Ed',
                'email' => 'john.smith@edugenis.com',
                'nip' => '198905202015011003',
                'bidang_keahlian' => 'Bahasa Inggris',
                'mata_pelajaran' => ['ENG'],
            ],
            [
                'nama' => 'Prof. Bambang Sutrisno',
                'email' => 'bambang.sutrisno@edugenis.com',
                'nip' => '197812101998031004',
                'bidang_keahlian' => 'Fisika',
                'mata_pelajaran' => ['FIS'],
            ],
            [
                'nama' => 'Dr. Rina Kusumawati',
                'email' => 'rina.kusumawati@edugenis.com',
                'nip' => '198204252008012005',
                'bidang_keahlian' => 'Kimia',
                'mata_pelajaran' => ['KIM'],
            ],
            [
                'nama' => 'Drs. Hadi Purnomo',
                'email' => 'hadi.purnomo@edugenis.com',
                'nip' => '197606101999031006',
                'bidang_keahlian' => 'Biologi',
                'mata_pelajaran' => ['BIO'],
            ],
        ];

        foreach ($teachers as $teacherData) {
            $user = User::create([
                'nama' => $teacherData['nama'],
                'email' => $teacherData['email'],
                'password' => Hash::make('guru123'),
                'nomor_telepon' => '0812345678' . rand(10, 99),
                'alamat' => 'Jl. Guru No. ' . rand(1, 100) . ', Jakarta',
                'tanggal_lahir' => '198' . rand(0, 9) . '-0' . rand(1, 9) . '-' . rand(10, 28),
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('guru');

            $guru = Guru::create([
                'user_id' => $user->id,
                'nip' => $teacherData['nip'],
                'bidang_keahlian' => $teacherData['bidang_keahlian'],
                'status_kepegawaian' => 'PNS',
                'tanggal_mulai_kerja' => '2020-07-01',
            ]);

            // Assign mata pelajaran
            $mataPelajaranIds = MataPelajaran::whereIn('kode_mata_pelajaran', $teacherData['mata_pelajaran'])->pluck('id');
            $guru->mataPelajaran()->attach($mataPelajaranIds);
        }

        // Create Kelas
        $kelas = [
            [
                'nama_kelas' => 'X IPA 1',
                'tingkat' => '10',
                'jurusan' => 'IPA',
                'kapasitas_maksimal' => 30,
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'X IPA 2',
                'tingkat' => '10',
                'jurusan' => 'IPA',
                'kapasitas_maksimal' => 30,
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XI IPA 1',
                'tingkat' => '11',
                'jurusan' => 'IPA',
                'kapasitas_maksimal' => 28,
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XII IPA 1',
                'tingkat' => '12',
                'jurusan' => 'IPA',
                'kapasitas_maksimal' => 25,
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ],
        ];

        foreach ($kelas as $kelasData) {
            Kelas::create($kelasData);
        }
    }
}
