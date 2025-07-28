<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriResource\Pages;
use App\Models\Materi;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Materi Pembelajaran';

    protected static ?string $modelLabel = 'Materi';

    protected static ?string $pluralModelLabel = 'Materi Pembelajaran';

    protected static ?string $navigationGroup = 'Pembelajaran';

    protected static ?int $navigationSort = 1;

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
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->whereIn('name', ['admin', 'guru'])->exists();
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Admin bisa edit semua, guru hanya bisa edit materi sendiri
        if (\Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
            $query->where('model_id', $user->id);
        })->where('name', 'admin')->exists()) {
            return true;
        }

        // Cek apakah guru ini adalah pemilik materi
        $guru = Guru::where('user_id', $user->id)->first();
        return $guru && $record->guru_id === $guru->id;
    }

    public static function canDelete($record): bool
    {
        return static::canEdit($record);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        // Admin bisa lihat semua materi
        if (\Spatie\Permission\Models\Role::whereHas('users', function($q) use ($user) {
            $q->where('model_id', $user->id);
        })->where('name', 'admin')->exists()) {
            return $query;
        }

        // Guru hanya bisa lihat materi sendiri
        $guru = Guru::where('user_id', $user->id)->first();
        if ($guru) {
            return $query->where('guru_id', $guru->id);
        }

        return $query->whereRaw('1 = 0'); // Return empty result
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Materi')
                    ->schema([
                        Forms\Components\TextInput::make('judul_materi')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Materi')
                            ->placeholder('Masukkan judul materi pembelajaran'),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi singkat tentang materi ini')
                            ->rows(3),

                        Forms\Components\Select::make('tipe_materi')
                            ->options([
                                'dokumen' => 'Dokumen',
                                'video' => 'Video',
                                'presentasi' => 'Presentasi',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->default('dokumen')
                            ->label('Tipe Materi'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->helperText('Materi yang dipublikasikan dapat dilihat oleh siswa')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Target Pembelajaran')
                    ->schema([
                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->relationship('mataPelajaran', 'nama_mata_pelajaran')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Mata Pelajaran')
                            ->helperText('Pilih mata pelajaran untuk materi ini'),

                        Forms\Components\Select::make('kelas_id')
                            ->relationship('kelas', 'nama_kelas')
                            ->searchable()
                            ->preload()
                            ->label('Kelas Target')
                            ->helperText('Pilih kelas target (opsional, kosongkan jika untuk semua kelas)'),

                        Forms\Components\Hidden::make('guru_id')
                            ->default(function () {
                                $user = Auth::user();
                                if ($user) {
                                    $guru = Guru::where('user_id', $user->id)->first();
                                    return $guru ? $guru->id : null;
                                }
                                return null;
                            }),
                    ])->columns(2),

                Forms\Components\Section::make('File Materi')
                    ->schema([
                        Forms\Components\FileUpload::make('files')
                            ->multiple()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                'video/mp4',
                                'video/avi',
                                'video/quicktime',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->label('Upload File')
                            ->helperText('Upload file materi pembelajaran (PDF, DOC, PPT, Video, Gambar)')
                            ->downloadable()
                            ->openable()
                            ->deletable()
                            ->reorderable()
                            ->directory('materi')
                            ->visibility('private'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_materi')
                    ->searchable()
                    ->sortable()
                    ->label('Judul Materi')
                    ->limit(50),

                Tables\Columns\TextColumn::make('guru.user.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Guru'),

                Tables\Columns\TextColumn::make('mataPelajaran.nama_mata_pelajaran')
                    ->searchable()
                    ->sortable()
                    ->label('Mata Pelajaran'),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->searchable()
                    ->sortable()
                    ->label('Kelas')
                    ->placeholder('Semua Kelas'),

                Tables\Columns\BadgeColumn::make('tipe_materi')
                    ->colors([
                        'primary' => 'dokumen',
                        'success' => 'video',
                        'warning' => 'presentasi',
                        'secondary' => 'lainnya',
                    ])
                    ->label('Tipe'),

                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama_mata_pelajaran')
                    ->label('Mata Pelajaran'),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Kelas'),

                Tables\Filters\SelectFilter::make('tipe_materi')
                    ->options([
                        'dokumen' => 'Dokumen',
                        'video' => 'Video',
                        'presentasi' => 'Presentasi',
                        'lainnya' => 'Lainnya',
                    ])
                    ->label('Tipe Materi'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListMateris::route('/'),
            'create' => Pages\CreateMateri::route('/create'),
            'edit' => Pages\EditMateri::route('/{record}/edit'),
        ];
    }
}
