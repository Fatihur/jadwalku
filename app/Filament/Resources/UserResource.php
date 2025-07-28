<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

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

        // Check if user has admin role using database query
        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->where('name', 'admin')->exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Role')
                    ->live(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Email'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8)
                    ->label('Password'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Status Aktif'),

                Forms\Components\Section::make('Pilih Guru')
                    ->schema([
                        Forms\Components\Select::make('guru_id')
                            ->label('Pilih Data Guru')
                            ->options(function (Forms\Get $get, ?string $operation, $record = null) {
                                $query = \App\Models\Guru::query();

                                if ($operation === 'create') {
                                    // Untuk create, hanya tampilkan guru yang belum punya user
                                    $query->whereDoesntHave('user');
                                } else {
                                    // Untuk edit, tampilkan guru yang belum punya user + guru yang sudah terhubung dengan user ini
                                    $currentGuruId = $record ? \App\Models\Guru::where('user_id', $record->id)->value('id') : null;
                                    $query->where(function($q) use ($currentGuruId) {
                                        $q->whereDoesntHave('user');
                                        if ($currentGuruId) {
                                            $q->orWhere('id', $currentGuruId);
                                        }
                                    });
                                }

                                return $query->get()->mapWithKeys(function ($guru) {
                                    return [$guru->id => "{$guru->nama_lengkap} ({$guru->nip}) - {$guru->bidang_keahlian}"];
                                });
                            })
                            ->searchable()
                            ->placeholder('Pilih guru dari data yang sudah ada')
                            ->helperText('Pilih guru yang akan dihubungkan dengan akun user ini')
                            ->required(fn (Forms\Get $get): bool =>
                                collect($get('roles'))->contains(function ($roleId) {
                                    $role = \Spatie\Permission\Models\Role::find($roleId);
                                    return $role && $role->name === 'guru';
                                })
                            ),

                        Forms\Components\Placeholder::make('guru_info')
                            ->label('Informasi')
                            ->content('Pilih data guru yang sudah ada di sistem untuk dihubungkan dengan akun user ini. Hanya guru yang belum memiliki akun yang akan ditampilkan.'),
                    ])
                    ->visible(fn (Forms\Get $get): bool =>
                        collect($get('roles'))->contains(function ($roleId) {
                            $role = \Spatie\Permission\Models\Role::find($roleId);
                            return $role && $role->name === 'guru';
                        })
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Role')
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'guru',
                        'warning' => 'siswa',
                    ]),

                Tables\Columns\TextColumn::make('guru.nip')
                    ->label('NIP')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('guru.bidang_keahlian')
                    ->label('Bidang Keahlian')
                    ->placeholder('-')
                    ->toggleable(),
                
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
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role'),
                
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles']);
    }
}
