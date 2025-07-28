<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hapus field yang tidak ada di tabel siswa
        unset($data['create_account']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $siswa = $this->record;
        $formData = $this->form->getState();

        // Cek apakah perlu membuat akun
        if ($formData['create_account'] ?? false) {
            $this->createUserAccount($siswa);
        }
    }

    protected function createUserAccount($siswa): void
    {
        try {
            // Generate email dan password
            $email = $this->generateStudentEmail($siswa->nama_lengkap, $siswa->nis);
            $password = $this->generateStudentPassword($siswa->nis);

            // Buat user account
            $user = User::create([
                'nama' => $siswa->nama_lengkap,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
            ]);

            // Assign role siswa
            $user->assignRole('siswa');

            // Update siswa dengan user_id
            $siswa->update(['user_id' => $user->id]);

            // Tampilkan notifikasi sukses dengan info login
            Notification::make()
                ->title('Akun siswa berhasil dibuat!')
                ->body("Email: {$email}\nPassword: {$password}")
                ->success()
                ->persistent()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal membuat akun siswa')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
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
}
