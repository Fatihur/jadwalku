<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_account')
                ->label('Buat Akun Login')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->visible(fn () => !$this->record->user_id)
                ->action(function () {
                    $this->createUserAccount($this->record);
                }),

            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->visible(fn () => $this->record->user_id)
                ->requiresConfirmation()
                ->modalHeading('Reset Password Siswa')
                ->modalDescription('Password akan direset ke default: siswa + NIS')
                ->action(function () {
                    $this->resetPassword($this->record);
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set create_account berdasarkan apakah sudah ada user_id
        $data['create_account'] = !empty($data['user_id']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus field yang tidak ada di tabel siswa
        unset($data['create_account']);

        return $data;
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

            // Refresh halaman
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $siswa]));

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal membuat akun siswa')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function resetPassword($siswa): void
    {
        try {
            if (!$siswa->user_id) {
                throw new \Exception('Siswa belum memiliki akun login');
            }

            $user = User::find($siswa->user_id);
            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }

            // Reset password ke default
            $newPassword = $this->generateStudentPassword($siswa->nis);
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            Notification::make()
                ->title('Password berhasil direset!')
                ->body("Password baru: {$newPassword}")
                ->success()
                ->persistent()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal reset password')
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
