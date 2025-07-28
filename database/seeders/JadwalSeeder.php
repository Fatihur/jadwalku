<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Ruangan;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus jadwal lama
        Jadwal::truncate();

        // Ambil data yang diperlukan
        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $guru = Guru::all();
        $ruangan = Ruangan::all();

        if ($kelas->isEmpty() || $mataPelajaran->isEmpty() || $guru->isEmpty() || $ruangan->isEmpty()) {
            $this->command->error('Data master belum lengkap. Pastikan ada data kelas, mata pelajaran, guru, dan ruangan.');
            return;
        }

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $jamSlots = [
            '07:00:00' => '08:00:00',
            '08:00:00' => '09:00:00',
            '09:00:00' => '10:00:00',
            '10:00:00' => '11:00:00',
            '11:00:00' => '12:00:00',
            '13:00:00' => '14:00:00', // Skip 12:00-13:00 (istirahat)
            '14:00:00' => '15:00:00',
            '15:00:00' => '16:00:00',
        ];

        $jadwalCount = 0;
        $globalSchedule = []; // Track semua jadwal untuk mencegah konflik ruangan dan guru

        foreach ($kelas as $kelasItem) {
            foreach ($hari as $hariItem) {
                $usedSlots = []; // Slot yang sudah digunakan untuk kelas ini
                $dailyScheduleCount = rand(3, 6); // 3-6 mata pelajaran per hari

                for ($i = 0; $i < $dailyScheduleCount; $i++) {
                    // Pilih slot waktu yang belum digunakan untuk kelas ini
                    $availableSlots = array_diff_key($jamSlots, $usedSlots);
                    if (empty($availableSlots)) break;

                    $jamMulai = array_rand($availableSlots);
                    $jamSelesai = $availableSlots[$jamMulai];

                    // Cari ruangan dan guru yang tidak konflik
                    $attempts = 0;
                    $maxAttempts = 50;
                    $ruanganId = null;
                    $guruId = null;

                    while ($attempts < $maxAttempts) {
                        $candidateRuangan = $ruangan->random()->id;
                        $candidateGuru = $guru->random()->id;

                        // Cek konflik ruangan dan guru pada waktu yang sama
                        $conflictKey = $hariItem . '_' . $jamMulai;
                        $hasConflict = false;

                        if (isset($globalSchedule[$conflictKey])) {
                            foreach ($globalSchedule[$conflictKey] as $existingSchedule) {
                                if ($existingSchedule['ruangan_id'] == $candidateRuangan ||
                                    $existingSchedule['guru_id'] == $candidateGuru) {
                                    $hasConflict = true;
                                    break;
                                }
                            }
                        }

                        if (!$hasConflict) {
                            $ruanganId = $candidateRuangan;
                            $guruId = $candidateGuru;
                            break;
                        }

                        $attempts++;
                    }

                    // Jika tidak bisa menemukan ruangan/guru yang tidak konflik, skip slot ini
                    if ($ruanganId === null || $guruId === null) {
                        continue;
                    }

                    // Buat jadwal
                    $jadwalData = [
                        'kelas_id' => $kelasItem->id,
                        'mata_pelajaran_id' => $mataPelajaran->random()->id,
                        'guru_id' => $guruId,
                        'ruangan_id' => $ruanganId,
                        'hari' => $hariItem,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'semester' => 'ganjil',
                        'tahun_ajaran' => '2024/2025',
                        'is_active' => true,
                    ];

                    Jadwal::create($jadwalData);

                    // Simpan ke global schedule untuk tracking konflik
                    $conflictKey = $hariItem . '_' . $jamMulai;
                    if (!isset($globalSchedule[$conflictKey])) {
                        $globalSchedule[$conflictKey] = [];
                    }
                    $globalSchedule[$conflictKey][] = $jadwalData;

                    // Mark slot sebagai terpakai untuk kelas ini
                    $usedSlots[$jamMulai] = true;
                    $jadwalCount++;
                }
            }
        }

        $this->command->info("Berhasil membuat {$jadwalCount} jadwal untuk semester ganjil 2024/2025");
    }
}
