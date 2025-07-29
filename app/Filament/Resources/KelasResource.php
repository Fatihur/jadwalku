<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Kelas';

    protected static ?string $navigationGroup = 'Manajemen Akademik';

    protected static ?int $navigationSort = 1;

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
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kelas')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kelas')
                            ->placeholder('Contoh: X IPA 1, XI IPS 2'),
                        
                        Forms\Components\Select::make('tingkat')
                            ->options([
                                '10' => 'Kelas 10',
                                '11' => 'Kelas 11',
                                '12' => 'Kelas 12',
                            ])
                            ->required()
                            ->label('Tingkat'),
                        
                        Forms\Components\TextInput::make('jurusan')
                            ->maxLength(255)
                            ->label('Jurusan')
                            ->placeholder('Contoh: IPA, IPS, Bahasa'),
                        
                        Forms\Components\Select::make('wali_kelas_id')
                            ->relationship('waliKelas.user', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Wali Kelas')
                            ->helperText('Pilih guru yang akan menjadi wali kelas'),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('kapasitas_maksimal')
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->maxValue(50)
                            ->required()
                            ->label('Kapasitas Maksimal'),
                        
                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->required()
                            ->maxLength(9)
                            ->placeholder('2024/2025')
                            ->label('Tahun Ajaran'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Status Aktif'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kelas')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kelas'),
                
                Tables\Columns\TextColumn::make('tingkat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '10' => 'primary',
                        '11' => 'success',
                        '12' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => "Kelas {$state}")
                    ->label('Tingkat'),
                
                Tables\Columns\TextColumn::make('jurusan')
                    ->searchable()
                    ->label('Jurusan'),
                
                Tables\Columns\TextColumn::make('waliKelas.user.nama')
                    ->searchable()
                    ->label('Wali Kelas'),
                
                Tables\Columns\TextColumn::make('siswa_count')
                    ->counts('siswa')
                    ->label('Jumlah Siswa'),
                
                Tables\Columns\TextColumn::make('kapasitas_maksimal')
                    ->label('Kapasitas'),
                
                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->searchable()
                    ->sortable()
                    ->label('Tahun Ajaran'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                    ])
                    ->label('Tingkat'),
                
                Tables\Filters\SelectFilter::make('tahun_ajaran')
                    ->options(function () {
                        return Kelas::distinct()
                            ->pluck('tahun_ajaran', 'tahun_ajaran')
                            ->toArray();
                    })
                    ->label('Tahun Ajaran'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'view' => Pages\ViewKelas::route('/{record}'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['waliKelas.user', 'siswa']);
    }
}
