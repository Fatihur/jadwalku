<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class JadwalKelasPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static string $view = 'filament.pages.jadwal-kelas-page';

    protected static ?string $navigationLabel = 'Jadwal Per Kelas';

    protected static ?string $title = 'Jadwal Per Kelas';

    public static function getNavigationLabel(): string
    {
        $user = Auth::user();
        if ($user && static::isGuru()) {
            return 'Jadwal Saya';
        }
        return 'Jadwal Per Kelas';
    }

    public function getTitle(): string
    {
        $user = Auth::user();
        if ($user && static::isGuru()) {
            return 'Jadwal Mengajar Saya';
        }
        return 'Jadwal Per Kelas';
    }

    protected static function isGuru(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->where('name', 'guru')->exists();
    }

    protected static ?string $navigationGroup = 'Manajemen Akademik';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->whereIn('name', ['admin', 'guru'])->exists();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public ?string $selectedSemester = 'ganjil';
    public ?string $selectedTahunAjaran = '2024/2025';
    public ?string $selectedHari = 'senin';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedSemester')
                    ->label('Semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    ])
                    ->default('ganjil')
                    ->live(),

                Select::make('selectedTahunAjaran')
                    ->label('Tahun Ajaran')
                    ->options([
                        '2024/2025' => '2024/2025',
                        '2025/2026' => '2025/2026',
                    ])
                    ->default('2024/2025')
                    ->live(),

                Select::make('selectedHari')
                    ->label('Hari')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                    ])
                    ->default('senin')
                    ->live(),
            ])
            ->columns(3);
    }

    public function getJadwalData(): Collection
    {
        $query = Jadwal::with(['kelas', 'mataPelajaran', 'guru.user', 'ruangan'])
            ->where('semester', $this->selectedSemester)
            ->where('tahun_ajaran', $this->selectedTahunAjaran)
            ->where('hari', $this->selectedHari)
            ->where('is_active', true);

        // Filter untuk guru - hanya jadwal mereka sendiri
        $user = Auth::user();
        if ($user && !\Spatie\Permission\Models\Role::whereHas('users', function($q) use ($user) {
            $q->where('model_id', $user->id);
        })->where('name', 'admin')->exists()) {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
            if ($guru) {
                $query->where('guru_id', $guru->id);
            } else {
                return collect(); // Return empty collection jika bukan guru
            }
        }

        return $query->get()->groupBy('kelas_id');
    }

    public function getKelasList(): Collection
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    public function getTimeSlots(): array
    {
        return [
            '07:00:00' => '07:00 - 08:00',
            '08:00:00' => '08:00 - 09:00',
            '09:00:00' => '09:00 - 10:00',
            '10:00:00' => '10:00 - 11:00',
            '11:00:00' => '11:00 - 12:00',
            '12:00:00' => '12:00 - 13:00',
            '13:00:00' => '13:00 - 14:00',
            '14:00:00' => '14:00 - 15:00',
            '15:00:00' => '15:00 - 16:00',
        ];
    }

    public function getJadwalForKelasAndTime($kelasId, $time): ?object
    {
        $jadwalData = $this->getJadwalData();

        if (!isset($jadwalData[$kelasId])) {
            return null;
        }

        return $jadwalData[$kelasId]->first(function ($jadwal) use ($time) {
            $jamMulai = $jadwal->jam_mulai;
            $jamMulaiString = is_object($jamMulai) ? $jamMulai->format('H:i:s') : $jamMulai;
            return $jamMulaiString === $time;
        });
    }

    public function getSubjectColors(): array
    {
        return [
            'Matematika' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300'],
            'Bahasa Indonesia' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300'],
            'Bahasa Inggris' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300'],
            'IPA' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300'],
            'IPS' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-300'],
            'Fisika' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-300'],
            'Kimia' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-800', 'border' => 'border-pink-300'],
            'Biologi' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'border' => 'border-teal-300'],
            'Sejarah' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300'],
            'Geografi' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'border' => 'border-cyan-300'],
            'Ekonomi' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300'],
            'Sosiologi' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-800', 'border' => 'border-violet-300'],
            'Pendidikan Agama' => ['bg' => 'bg-lime-100', 'text' => 'text-lime-800', 'border' => 'border-lime-300'],
            'Pendidikan Kewarganegaraan' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'border' => 'border-rose-300'],
            'Seni Budaya' => ['bg' => 'bg-fuchsia-100', 'text' => 'text-fuchsia-800', 'border' => 'border-fuchsia-300'],
            'Pendidikan Jasmani' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-800', 'border' => 'border-sky-300'],
            'Prakarya' => ['bg' => 'bg-stone-100', 'text' => 'text-stone-800', 'border' => 'border-stone-300'],
            'Teknologi Informasi' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'border' => 'border-slate-300'],
        ];
    }

    public function getSubjectCode($mataPelajaran): string
    {
        $codes = [
            'Matematika' => 'MTK',
            'Bahasa Indonesia' => 'BIND',
            'Bahasa Inggris' => 'BING',
            'IPA' => 'IPA',
            'IPS' => 'IPS',
            'Fisika' => 'FIS',
            'Kimia' => 'KIM',
            'Biologi' => 'BIO',
            'Sejarah' => 'SEJ',
            'Geografi' => 'GEO',
            'Ekonomi' => 'EKO',
            'Sosiologi' => 'SOS',
            'Pendidikan Agama' => 'PAI',
            'Pendidikan Kewarganegaraan' => 'PKN',
            'Seni Budaya' => 'SBK',
            'Pendidikan Jasmani' => 'PJOK',
            'Prakarya' => 'PKY',
            'Teknologi Informasi' => 'TIK',
        ];

        return $codes[$mataPelajaran] ?? strtoupper(substr($mataPelajaran, 0, 3));
    }
}
