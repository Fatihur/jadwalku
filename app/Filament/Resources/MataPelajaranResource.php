<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataPelajaranResource\Pages;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Mata Pelajaran';

    protected static ?string $modelLabel = 'Mata Pelajaran';

    protected static ?string $pluralModelLabel = 'Mata Pelajaran';

    protected static ?string $navigationGroup = 'Manajemen Akademik';

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
                Forms\Components\Section::make('Informasi Mata Pelajaran')
                    ->schema([
                        Forms\Components\TextInput::make('nama_mata_pelajaran')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Mata Pelajaran')
                            ->placeholder('Contoh: Matematika, Bahasa Indonesia'),
                        
                        Forms\Components\TextInput::make('kode_mata_pelajaran')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->label('Kode Mata Pelajaran')
                            ->placeholder('Contoh: MAT, BIN, ENG')
                            ->helperText('Kode unik untuk mata pelajaran'),
                        
                        Forms\Components\Textarea::make('deskripsi')
                            ->maxLength(500)
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi singkat tentang mata pelajaran'),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Pembelajaran')
                    ->schema([
                        Forms\Components\TextInput::make('jam_per_minggu')
                            ->numeric()
                            ->default(2)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->label('Jam Per Minggu')
                            ->helperText('Jumlah jam pelajaran per minggu'),
                        
                        Forms\Components\CheckboxList::make('tingkat')
                            ->options([
                                '10' => 'Kelas 10',
                                '11' => 'Kelas 11',
                                '12' => 'Kelas 12',
                            ])
                            ->required()
                            ->label('Tingkat Kelas')
                            ->helperText('Pilih tingkat kelas yang bisa mengambil mata pelajaran ini')
                            ->columns(3),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Status Aktif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_mata_pelajaran')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Mata Pelajaran'),
                
                Tables\Columns\TextColumn::make('kode_mata_pelajaran')
                    ->searchable()
                    ->sortable()
                    ->label('Kode'),
                
                Tables\Columns\TextColumn::make('jam_per_minggu')
                    ->numeric()
                    ->sortable()
                    ->label('Jam/Minggu'),
                
                Tables\Columns\TextColumn::make('tingkat')
                    ->badge()
                    ->separator(', ')
                    ->formatStateUsing(fn (string $state): string => "Kelas {$state}")
                    ->label('Tingkat'),
                
                Tables\Columns\TextColumn::make('guru_count')
                    ->counts('guru')
                    ->label('Jumlah Guru'),
                
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
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                
                Tables\Filters\Filter::make('jam_per_minggu')
                    ->form([
                        Forms\Components\TextInput::make('jam_min')
                            ->numeric()
                            ->label('Jam Minimum'),
                        Forms\Components\TextInput::make('jam_max')
                            ->numeric()
                            ->label('Jam Maksimum'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['jam_min'],
                                fn (Builder $query, $jam): Builder => $query->where('jam_per_minggu', '>=', $jam),
                            )
                            ->when(
                                $data['jam_max'],
                                fn (Builder $query, $jam): Builder => $query->where('jam_per_minggu', '<=', $jam),
                            );
                    })
                    ->label('Filter Jam Per Minggu'),
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
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'view' => Pages\ViewMataPelajaran::route('/{record}'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['guru']);
    }
}
