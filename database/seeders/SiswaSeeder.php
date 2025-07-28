<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasList = Kelas::all();

        if ($kelasList->isEmpty()) {
            $this->command->error('Tidak ada data kelas. Jalankan KelasSeeder terlebih dahulu.');
            return;
        }

        $siswaData = [
            // Kelas X IPA 1
            ['nama' => 'Ahmad Rizki Pratama', 'nisn' => '0051234567', 'nis' => '2024001'],
            ['nama' => 'Siti Nurhaliza Putri', 'nisn' => '0051234568', 'nis' => '2024002'],
            ['nama' => 'Budi Santoso', 'nisn' => '0051234569', 'nis' => '2024003'],
            ['nama' => 'Dewi Sartika', 'nisn' => '0051234570', 'nis' => '2024004'],
            ['nama' => 'Eko Prasetyo', 'nisn' => '0051234571', 'nis' => '2024005'],

            // Kelas X IPA 2
            ['nama' => 'Fitri Handayani', 'nisn' => '0051234572', 'nis' => '2024006'],
            ['nama' => 'Galih Permana', 'nisn' => '0051234573', 'nis' => '2024007'],
            ['nama' => 'Hani Safitri', 'nisn' => '0051234574', 'nis' => '2024008'],
            ['nama' => 'Indra Gunawan', 'nisn' => '0051234575', 'nis' => '2024009'],
            ['nama' => 'Jihan Aulia', 'nisn' => '0051234576', 'nis' => '2024010'],

            // Kelas X IPS 1
            ['nama' => 'Karina Salsabila', 'nisn' => '0051234577', 'nis' => '2024011'],
            ['nama' => 'Lukman Hakim', 'nisn' => '0051234578', 'nis' => '2024012'],
            ['nama' => 'Maya Anggraini', 'nisn' => '0051234579', 'nis' => '2024013'],
            ['nama' => 'Nanda Pratama', 'nisn' => '0051234580', 'nis' => '2024014'],
            ['nama' => 'Olivia Ramadhani', 'nisn' => '0051234581', 'nis' => '2024015'],

            // Kelas XI IPA 1
            ['nama' => 'Putra Wijaya', 'nisn' => '0051234582', 'nis' => '2023001'],
            ['nama' => 'Qonita Zahira', 'nisn' => '0051234583', 'nis' => '2023002'],
            ['nama' => 'Rafi Maulana', 'nisn' => '0051234584', 'nis' => '2023003'],
            ['nama' => 'Salma Kamila', 'nisn' => '0051234585', 'nis' => '2023004'],
            ['nama' => 'Taufik Hidayat', 'nisn' => '0051234586', 'nis' => '2023005'],

            // Kelas XI IPS 1
            ['nama' => 'Ulfa Maharani', 'nisn' => '0051234587', 'nis' => '2023006'],
            ['nama' => 'Vino Bastian', 'nisn' => '0051234588', 'nis' => '2023007'],
            ['nama' => 'Wulan Dari', 'nisn' => '0051234589', 'nis' => '2023008'],
            ['nama' => 'Yoga Pratama', 'nisn' => '0051234590', 'nis' => '2023009'],
            ['nama' => 'Zahra Amelia', 'nisn' => '0051234591', 'nis' => '2023010'],

            // Kelas XII IPA 1
            ['nama' => 'Arief Rahman', 'nisn' => '0051234592', 'nis' => '2022001'],
            ['nama' => 'Bella Safira', 'nisn' => '0051234593', 'nis' => '2022002'],
            ['nama' => 'Candra Kirana', 'nisn' => '0051234594', 'nis' => '2022003'],
            ['nama' => 'Dimas Anggara', 'nisn' => '0051234595', 'nis' => '2022004'],
            ['nama' => 'Elsa Purnama', 'nisn' => '0051234596', 'nis' => '2022005'],
        ];

        $kelasIndex = 0;
        $siswaPerKelas = 5;
        $currentCount = 0;

        foreach ($siswaData as $index => $data) {
            // Tentukan kelas berdasarkan index
            if ($currentCount >= $siswaPerKelas) {
                $kelasIndex++;
                $currentCount = 0;
            }

            $kelas = $kelasList->get($kelasIndex % $kelasList->count());

            // Tentukan tahun masuk berdasarkan NIS
            $tahunMasuk = 2024;
            if (str_starts_with($data['nis'], '2023')) {
                $tahunMasuk = 2023;
            } elseif (str_starts_with($data['nis'], '2022')) {
                $tahunMasuk = 2022;
            }

            Siswa::create([
                'nama_lengkap' => $data['nama'],
                'kelas_id' => $kelas->id,
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'tahun_masuk' => $tahunMasuk,
                'status_siswa' => 'aktif',
                'nama_orang_tua' => $this->generateParentName($data['nama']),
                'nomor_telepon_orang_tua' => $this->generatePhoneNumber(),
            ]);

            $currentCount++;
        }

        $this->command->info('Berhasil membuat ' . count($siswaData) . ' data siswa');
    }

    private function generateParentName($studentName): string
    {
        $parentPrefixes = ['Bapak', 'Ibu'];
        $names = ['Suryanto', 'Hartono', 'Wijaya', 'Sari', 'Indrawati', 'Kusuma', 'Pratama', 'Santoso'];

        $prefix = $parentPrefixes[array_rand($parentPrefixes)];
        $name = $names[array_rand($names)];

        return $prefix . ' ' . $name;
    }

    private function generatePhoneNumber(): string
    {
        $prefixes = ['0812', '0813', '0821', '0822', '0851', '0852'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

        return $prefix . $number;
    }
}
