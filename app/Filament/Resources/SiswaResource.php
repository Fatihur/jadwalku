<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->where('name', 'admin')->exists();
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

    public static function shouldRegisterNavigation(): bool
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

    protected function generateStudentEmail(string $nama, string $nis): string
    {
        // Ambil nama depan dan bersihkan
        $namaDepan = strtolower(explode(' ', $nama)[0]);
        $namaDepan = preg_replace('/[^a-z]/', '', $namaDepan);

        // Gabungkan dengan NIS
        return $namaDepan . '.' . $nis . '@siswa.sekolah.com';
    }

    protected function generateStudentPassword(string $nis): string
    {
        // Password default: siswa + NIS
        return 'siswa' . $nis;
    }

    public static function generateStudentEmailStatic(string $nama, string $nis): string
    {
        // Ambil nama depan dan bersihkan
        $namaDepan = strtolower(explode(' ', $nama)[0]);
        $namaDepan = preg_replace('/[^a-z]/', '', $namaDepan);

        // Gabungkan dengan NIS
        return $namaDepan . '.' . $nis . '@siswa.sekolah.com';
    }

    public static function generateStudentPasswordStatic(string $nis): string
    {
        // Password default: siswa + NIS
        return 'siswa' . $nis;
    }

    public function createBulkAccounts($records): void
    {
        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($records as $siswa) {
            // Skip jika sudah ada akun
            if ($siswa->user_id) {
                $skipped++;
                continue;
            }

            try {
                // Generate email dan password
                $email = static::generateStudentEmailStatic($siswa->nama_lengkap, $siswa->nis);
                $password = static::generateStudentPasswordStatic($siswa->nis);

                // Buat user account
                $user = \App\Models\User::create([
                    'nama' => $siswa->nama_lengkap,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'is_active' => true,
                ]);

                // Assign role siswa
                $user->assignRole('siswa');

                // Update siswa dengan user_id
                $siswa->update(['user_id' => $user->id]);

                $created++;

            } catch (\Exception $e) {
                $errors[] = "Gagal membuat akun untuk {$siswa->nama_lengkap}: " . $e->getMessage();
            }
        }

        // Tampilkan notifikasi hasil
        $message = "Berhasil membuat {$created} akun";
        if ($skipped > 0) {
            $message .= ", {$skipped} dilewati (sudah ada akun)";
        }
        if (!empty($errors)) {
            $message .= "\n\nError:\n" . implode("\n", $errors);
        }

        \Filament\Notifications\Notification::make()
            ->title('Pembuatan Akun Selesai')
            ->body($message)
            ->success()
            ->persistent()
            ->send();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap')
                    ->placeholder('Nama lengkap siswa'),

                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Kelas'),

                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10)
                    ->label('NISN')
                    ->placeholder('Nomor Induk Siswa Nasional'),

                Forms\Components\TextInput::make('nis')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10)
                    ->label('NIS')
                    ->placeholder('Nomor Induk Siswa'),

                Forms\Components\Select::make('tahun_masuk')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 10; $i <= $currentYear + 2; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->required()
                    ->default(date('Y'))
                    ->label('Tahun Masuk'),

                Forms\Components\Select::make('status_siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'pindah' => 'Pindah',
                        'keluar' => 'Keluar',
                    ])
                    ->required()
                    ->default('aktif')
                    ->label('Status Siswa'),

                Forms\Components\TextInput::make('nama_orang_tua')
                    ->maxLength(255)
                    ->label('Nama Orang Tua/Wali'),

                Forms\Components\TextInput::make('nomor_telepon_orang_tua')
                    ->tel()
                    ->maxLength(20)
                    ->label('Nomor Telepon Orang Tua'),

                Forms\Components\Section::make('Akun Login Siswa')
                    ->schema([
                        Forms\Components\Toggle::make('create_account')
                            ->label('Buat Akun Login untuk Siswa')
                            ->default(false)
                            ->live()
                            ->helperText('Jika diaktifkan, akan membuat akun login otomatis untuk siswa'),

                        Forms\Components\Placeholder::make('account_info')
                            ->label('Informasi Akun')
                            ->content(function (Forms\Get $get) {
                                if ($get('create_account')) {
                                    $nama = $get('nama_lengkap');
                                    $nis = $get('nis');

                                    if ($nama && $nis) {
                                        $email = static::generateStudentEmailStatic($nama, $nis);
                                        $password = static::generateStudentPasswordStatic($nis);

                                        return "Email: {$email}\nPassword: {$password}";
                                    }

                                    return 'Email dan password akan dibuat otomatis berdasarkan nama dan NIS';
                                }

                                return 'Tidak akan membuat akun login';
                            })
                            ->visible(fn (Forms\Get $get) => $get('create_account')),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Siswa'),

                Tables\Columns\TextColumn::make('nisn')
                    ->searchable()
                    ->sortable()
                    ->label('NISN'),

                Tables\Columns\TextColumn::make('nis')
                    ->searchable()
                    ->sortable()
                    ->label('NIS'),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->searchable()
                    ->sortable()
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('tahun_masuk')
                    ->sortable()
                    ->label('Tahun Masuk'),

                Tables\Columns\TextColumn::make('status_siswa')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'lulus' => 'primary',
                        'pindah' => 'warning',
                        'keluar' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),

                Tables\Columns\TextColumn::make('nama_orang_tua')
                    ->searchable()
                    ->label('Nama Orang Tua')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nomor_telepon_orang_tua')
                    ->label('No. Telepon Orang Tua')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email Login')
                    ->placeholder('Belum ada akun')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_account')
                    ->label('Akun Login')
                    ->boolean()
                    ->state(fn ($record) => !is_null($record->user_id))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Kelas'),

                Tables\Filters\SelectFilter::make('status_siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'pindah' => 'Pindah',
                        'keluar' => 'Keluar',
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('tahun_masuk')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 10; $i <= $currentYear; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->label('Tahun Masuk'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('create_accounts')
                        ->label('Buat Akun Login')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Buat Akun Login untuk Siswa Terpilih')
                        ->modalDescription('Akun login akan dibuat otomatis untuk siswa yang belum memiliki akun.')
                        ->action(function ($records) {
                            $this->createBulkAccounts($records);
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
