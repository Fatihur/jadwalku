<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'nama' => 'Administrator EduGenis',
            'email' => 'admin@edugenis.com',
            'password' => Hash::make('admin123'),
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Pendidikan No. 1, Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        // Create sample teacher user
        $guru = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'guru@edugenis.com',
            'password' => Hash::make('guru123'),
            'nomor_telepon' => '081234567891',
            'alamat' => 'Jl. Guru No. 2, Jakarta',
            'tanggal_lahir' => '1985-05-15',
            'jenis_kelamin' => 'L',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign guru role
        $guru->assignRole('guru');

        // Create sample student user
        $siswa = User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siswa@edugenis.com',
            'password' => Hash::make('siswa123'),
            'nomor_telepon' => '081234567892',
            'alamat' => 'Jl. Siswa No. 3, Jakarta',
            'tanggal_lahir' => '2007-08-20',
            'jenis_kelamin' => 'P',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign siswa role
        $siswa->assignRole('siswa');
    }
}
