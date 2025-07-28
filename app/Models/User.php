<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements HasMedia, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'name', // For compatibility with Filament
        'email',
        'password',
        'nomor_telepon',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user can access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'guru']);
    }

    /**
     * Get the name for Filament
     */
    public function getFilamentName(): string
    {
        return $this->nama ?? $this->email;
    }

    /**
     * Get the name attribute for compatibility
     */
    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    /**
     * Relationship dengan Guru
     */
    public function guru(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guru::class);
    }

    /**
     * Set the name attribute for compatibility
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }



    /**
     * Relationship dengan Siswa
     */
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}
