<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;

class CheckJadwalConflicts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:check-conflicts {--fix : Automatically fix conflicts by removing duplicates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for scheduling conflicts (room, teacher, class conflicts)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for scheduling conflicts...');

        $conflicts = $this->findConflicts();

        if (empty($conflicts)) {
            $this->info('✅ No conflicts found! All schedules are valid.');
            return;
        }

        $this->error("❌ Found " . count($conflicts) . " conflicts:");

        foreach ($conflicts as $conflict) {
            $this->line('');
            $this->warn("Conflict Type: {$conflict['type']}");
            $this->line("Time: {$conflict['hari']} {$conflict['jam_mulai']}");
            $this->line("Conflicting schedules:");

            foreach ($conflict['schedules'] as $schedule) {
                $this->line("  - ID: {$schedule->id} | Kelas: {$schedule->kelas->nama_kelas} | Mapel: {$schedule->mataPelajaran->nama_mata_pelajaran} | Guru: {$schedule->guru->user->name} | Ruangan: {$schedule->ruangan->nama_ruangan}");
            }
        }

        if ($this->option('fix')) {
            $this->fixConflicts($conflicts);
        } else {
            $this->line('');
            $this->info('💡 Run with --fix option to automatically resolve conflicts');
        }
    }

    private function findConflicts()
    {
        $conflicts = [];

        // Group jadwal by time slot
        $jadwalGroups = Jadwal::with(['kelas', 'mataPelajaran', 'guru.user', 'ruangan'])
            ->where('is_active', true)
            ->get()
            ->groupBy(function ($jadwal) {
                return $jadwal->hari . '_' . $jadwal->jam_mulai;
            });

        foreach ($jadwalGroups as $timeSlot => $jadwals) {
            if ($jadwals->count() <= 1) continue;

            [$hari, $jamMulai] = explode('_', $timeSlot);

            // Check room conflicts
            $roomGroups = $jadwals->groupBy('ruangan_id');
            foreach ($roomGroups as $ruanganId => $roomJadwals) {
                if ($roomJadwals->count() > 1) {
                    $conflicts[] = [
                        'type' => 'Room Conflict',
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'resource_id' => $ruanganId,
                        'schedules' => $roomJadwals
                    ];
                }
            }

            // Check teacher conflicts
            $teacherGroups = $jadwals->groupBy('guru_id');
            foreach ($teacherGroups as $guruId => $teacherJadwals) {
                if ($teacherJadwals->count() > 1) {
                    $conflicts[] = [
                        'type' => 'Teacher Conflict',
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'resource_id' => $guruId,
                        'schedules' => $teacherJadwals
                    ];
                }
            }

            // Check class conflicts
            $classGroups = $jadwals->groupBy('kelas_id');
            foreach ($classGroups as $kelasId => $classJadwals) {
                if ($classJadwals->count() > 1) {
                    $conflicts[] = [
                        'type' => 'Class Conflict',
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'resource_id' => $kelasId,
                        'schedules' => $classJadwals
                    ];
                }
            }
        }

        return $conflicts;
    }

    private function fixConflicts($conflicts)
    {
        $this->info('🔧 Fixing conflicts...');

        $deletedCount = 0;

        foreach ($conflicts as $conflict) {
            $schedules = $conflict['schedules'];

            // Keep the first schedule, delete the rest
            $deleteSchedules = $schedules->slice(1);

            foreach ($deleteSchedules as $schedule) {
                $this->line("Deleting duplicate: ID {$schedule->id} - {$schedule->mataPelajaran->nama_mata_pelajaran}");
                $schedule->delete();
                $deletedCount++;
            }
        }

        $this->info("✅ Fixed conflicts! Deleted {$deletedCount} duplicate schedules.");
    }
}
