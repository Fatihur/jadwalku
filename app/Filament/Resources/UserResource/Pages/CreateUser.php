<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Guru;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Simpan guru_id untuk diproses nanti
        $this->guruId = $data['guru_id'] ?? null;

        // Set nama sementara jika belum ada
        if (!isset($data['nama']) || empty($data['nama'])) {
            $data['nama'] = 'User Temp'; // Akan diupdate nanti dari data guru
        }

        // Hapus guru_id dari data utama
        unset($data['guru_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        // Cek apakah user memiliki role guru dan ada guru_id
        if ($user->hasRole('guru') && $this->guruId) {
            // Ambil data guru
            $guru = Guru::find($this->guruId);

            if ($guru) {
                // Update guru dengan user_id
                $guru->update(['user_id' => $user->id]);

                // Update nama user dengan nama dari data guru
                if ($guru->nama_lengkap) {
                    $user->update([
                        'nama' => $guru->nama_lengkap
                    ]);
                }
            }
        }
    }

    protected $guruId = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
