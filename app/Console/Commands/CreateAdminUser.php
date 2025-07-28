<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'edugenis:create-admin';
    protected $description = 'Create an admin user for EduGenis';

    public function handle()
    {
        $this->info('Creating Admin User for EduGenis');
        $this->line('');

        $nama = $this->ask('Nama lengkap');
        $email = $this->ask('Email');
        $password = $this->secret('Password');

        // Validate input
        $validator = Validator::make([
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
        ], [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - {$error}");
            }
            return 1;
        }

        try {
            $user = User::create([
                'nama' => $nama,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('admin');

            $this->info('Admin user created successfully!');
            $this->line('');
            $this->line("Nama: {$nama}");
            $this->line("Email: {$email}");
            $this->line('');
            $this->line('You can now login to the admin panel at: /admin');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}
