<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalResource\Pages;
use App\Models\Jadwal;
use App\Services\ScheduleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    protected static ?string $modelLabel = 'Jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?string $navigationGroup = 'Manajemen Akademik';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->whereIn('name', ['admin', 'guru'])->exists();
    }

    public static function canCreate(): bool
    {
        return static::isAdmin();
    }

    public static function canEdit($record): bool
    {
        return static::isAdmin();
    }

    public static function canDelete($record): bool
    {
        return static::isAdmin();
    }

    protected static function isAdmin(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->where('name', 'admin')->exists();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery()->with(['kelas', 'mataPelajaran', 'guru.user', 'ruangan']);

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        // Admin bisa lihat semua jadwal
        if (\Spatie\Permission\Models\Role::whereHas('users', function($q) use ($user) {
            $q->where('model_id', $user->id);
        })->where('name', 'admin')->exists()) {
            return $query;
        }

        // Guru hanya bisa lihat jadwal mereka sendiri
        $guru = \App\Models\Guru::where('user_id', $user->id)->first();
        if ($guru) {
            return $query->where('guru_id', $guru->id);
        }

        return $query->whereRaw('1 = 0'); // Return empty result
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jadwal')
                    ->schema([
                        Forms\Components\Select::make('kelas_id')
                            ->relationship('kelas', 'nama_kelas')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Kelas'),
                        
                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->relationship('mataPelajaran', 'nama_mata_pelajaran')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Mata Pelajaran'),
                        
                        Forms\Components\Select::make('guru_id')
                            ->relationship('guru.user', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Guru'),
                        
                        Forms\Components\Select::make('ruangan_id')
                            ->relationship('ruangan', 'nama_ruangan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Ruangan'),
                    ])->columns(2),

                Forms\Components\Section::make('Waktu Pembelajaran')
                    ->schema([
                        Forms\Components\Select::make('hari')
                            ->options([
                                'senin' => 'Senin',
                                'selasa' => 'Selasa',
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jumat',
                                'sabtu' => 'Sabtu',
                            ])
                            ->required()
                            ->label('Hari'),
                        
                        Forms\Components\TimePicker::make('jam_mulai')
                            ->required()
                            ->label('Jam Mulai')
                            ->seconds(false),
                        
                        Forms\Components\TimePicker::make('jam_selesai')
                            ->required()
                            ->label('Jam Selesai')
                            ->seconds(false)
                            ->after('jam_mulai'),
                    ])->columns(3),

                Forms\Components\Section::make('Periode Akademik')
                    ->schema([
                        Forms\Components\Select::make('semester')
                            ->options([
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                            ])
                            ->required()
                            ->label('Semester'),
                        
                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->required()
                            ->maxLength(9)
                            ->placeholder('2024/2025')
                            ->label('Tahun Ajaran'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Status Aktif'),
                    ])->columns(3),
            ])
            ->rules([
                function () {
                    return function (string $attribute, $value, \Closure $fail) {
                        // Get form data
                        $data = request()->all();

                        if (!isset($data['kelas_id'], $data['guru_id'], $data['ruangan_id'],
                                  $data['hari'], $data['jam_mulai'], $data['semester'], $data['tahun_ajaran'])) {
                            return; // Skip validation if required fields are missing
                        }

                        $recordId = request()->route('record') ? request()->route('record')->id : null;

                        // Check for conflicts
                        if (Jadwal::hasClassConflict(
                            $data['kelas_id'],
                            $data['hari'],
                            $data['jam_mulai'],
                            $data['semester'],
                            $data['tahun_ajaran'],
                            $recordId
                        )) {
                            $fail('Kelas sudah memiliki jadwal pada waktu yang sama.');
                        }

                        if (Jadwal::hasTeacherConflict(
                            $data['guru_id'],
                            $data['hari'],
                            $data['jam_mulai'],
                            $data['semester'],
                            $data['tahun_ajaran'],
                            $recordId
                        )) {
                            $fail('Guru sudah memiliki jadwal mengajar pada waktu yang sama.');
                        }

                        if (Jadwal::hasRoomConflict(
                            $data['ruangan_id'],
                            $data['hari'],
                            $data['jam_mulai'],
                            $data['semester'],
                            $data['tahun_ajaran'],
                            $recordId
                        )) {
                            $fail('Ruangan sudah digunakan pada waktu yang sama.');
                        }
                    };
                },
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->searchable()
                    ->sortable()
                    ->label('Kelas'),
                
                Tables\Columns\TextColumn::make('mataPelajaran.nama_mata_pelajaran')
                    ->searchable()
                    ->label('Mata Pelajaran'),
                
                Tables\Columns\TextColumn::make('guru.user.nama')
                    ->searchable()
                    ->label('Guru'),
                
                Tables\Columns\TextColumn::make('ruangan.nama_ruangan')
                    ->searchable()
                    ->label('Ruangan'),
                
                Tables\Columns\TextColumn::make('hari')
                    ->colors([
                        'primary' => 'senin',
                        'success' => 'selasa',
                        'warning' => 'rabu',
                        'danger' => 'kamis',
                        'secondary' => 'jumat',
                        'gray' => 'sabtu',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->label('Hari'),
                
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->time('H:i')
                    ->sortable()
                    ->label('Jam Mulai'),
                
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->time('H:i')
                    ->sortable()
                    ->label('Jam Selesai'),
                
                Tables\Columns\TextColumn::make('semester')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->label('Semester'),
                
                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->searchable()
                    ->sortable()
                    ->label('Tahun Ajaran'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Kelas'),
                
                Tables\Filters\SelectFilter::make('guru_id')
                    ->relationship('guru.user', 'nama')
                    ->label('Guru'),
                
                Tables\Filters\SelectFilter::make('hari')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                    ])
                    ->label('Hari'),
                
                Tables\Filters\SelectFilter::make('semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    ])
                    ->label('Semester'),
                
                Tables\Filters\SelectFilter::make('tahun_ajaran')
                    ->options(function () {
                        return Jadwal::distinct()
                            ->pluck('tahun_ajaran', 'tahun_ajaran')
                            ->toArray();
                    })
                    ->label('Tahun Ajaran'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate_schedule')
                    ->label('Generate Jadwal Otomatis')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->required()
                            ->placeholder('2024/2025')
                            ->label('Tahun Ajaran'),
                        Forms\Components\Select::make('semester')
                            ->options([
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                            ])
                            ->required()
                            ->label('Semester'),
                    ])
                    ->action(function (array $data) {
                        $scheduleService = new ScheduleService();
                        $result = $scheduleService->generateAndSaveSchedule(
                            $data['tahun_ajaran'],
                            $data['semester']
                        );
                        
                        if ($result['success']) {
                            Notification::make()
                                ->title('Jadwal berhasil dibuat!')
                                ->body("Fitness: " . number_format($result['fitness'] * 100, 2) . "%, Konflik: " . $result['conflict_count'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal membuat jadwal')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Proses ini akan menghapus jadwal lama dan membuat jadwal baru menggunakan algoritma genetika.'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('hari')
            ->defaultSort('jam_mulai');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'view' => Pages\ViewJadwal::route('/{record}'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }


}
