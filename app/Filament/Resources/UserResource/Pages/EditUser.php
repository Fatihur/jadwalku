<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Guru;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load guru_id jika ada
        $user = $this->record;
        $guru = Guru::where('user_id', $user->id)->first();

        if ($guru) {
            $data['guru_id'] = $guru->id;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Simpan guru_id untuk diproses nanti
        $this->guruId = $data['guru_id'] ?? null;
        $this->oldGuruId = Guru::where('user_id', $this->record->id)->value('id');

        // Hapus guru_id dari data utama
        unset($data['guru_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;

        // Lepas hubungan guru lama jika ada
        if ($this->oldGuruId) {
            Guru::where('id', $this->oldGuruId)->update(['user_id' => null]);
        }

        // Cek apakah user memiliki role guru dan ada guru_id baru
        if ($user->hasRole('guru') && $this->guruId) {
            // Ambil data guru baru
            $guru = Guru::find($this->guruId);

            if ($guru) {
                // Update guru dengan user_id baru
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
    protected $oldGuruId = null;
}
