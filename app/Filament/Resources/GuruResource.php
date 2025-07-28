<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use App\Models\User;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Guru';

    protected static ?string $modelLabel = 'Guru';

    protected static ?string $pluralModelLabel = 'Guru';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return static::isAdmin();
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Guru')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pengguna')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->label('Nama Lengkap'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique()
                                    ->label('Email'),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->label('Password'),
                                Forms\Components\TextInput::make('nomor_telepon')
                                    ->tel()
                                    ->label('Nomor Telepon'),
                            ]),
                        
                        Forms\Components\TextInput::make('nip')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->label('NIP (Nomor Induk Pegawai)'),
                        
                        Forms\Components\TextInput::make('bidang_keahlian')
                            ->maxLength(255)
                            ->label('Bidang Keahlian'),
                        
                        Forms\Components\Select::make('status_kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'GTT' => 'Guru Tidak Tetap',
                                'GTY' => 'Guru Tetap Yayasan',
                            ])
                            ->default('GTT')
                            ->required()
                            ->label('Status Kepegawaian'),
                        
                        Forms\Components\DatePicker::make('tanggal_mulai_kerja')
                            ->label('Tanggal Mulai Kerja')
                            ->maxDate(now()),
                    ])->columns(2),

                Forms\Components\Section::make('Mata Pelajaran')
                    ->schema([
                        Forms\Components\Select::make('mataPelajaran')
                            ->relationship('mataPelajaran', 'nama_mata_pelajaran')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Mata Pelajaran yang Diampu')
                            ->helperText('Pilih mata pelajaran yang bisa diajar oleh guru ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                
                Tables\Columns\TextColumn::make('nip')
                    ->searchable()
                    ->sortable()
                    ->label('NIP'),
                
                Tables\Columns\TextColumn::make('bidang_keahlian')
                    ->searchable()
                    ->label('Bidang Keahlian'),
                
                Tables\Columns\BadgeColumn::make('status_kepegawaian')
                    ->colors([
                        'success' => 'PNS',
                        'primary' => 'PPPK',
                        'warning' => 'GTT',
                        'secondary' => 'GTY',
                    ])
                    ->label('Status'),
                
                Tables\Columns\TextColumn::make('mataPelajaran.nama_mata_pelajaran')
                    ->badge()
                    ->separator(', ')
                    ->label('Mata Pelajaran'),
                
                Tables\Columns\TextColumn::make('tanggal_mulai_kerja')
                    ->date()
                    ->sortable()
                    ->label('Mulai Kerja'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kepegawaian')
                    ->options([
                        'PNS' => 'PNS',
                        'PPPK' => 'PPPK',
                        'GTT' => 'Guru Tidak Tetap',
                        'GTY' => 'Guru Tetap Yayasan',
                    ])
                    ->label('Status Kepegawaian'),
                
                Tables\Filters\SelectFilter::make('mataPelajaran')
                    ->relationship('mataPelajaran', 'nama_mata_pelajaran')
                    ->label('Mata Pelajaran'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'view' => Pages\ViewGuru::route('/{record}'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'mataPelajaran']);
    }
}
