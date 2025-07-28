<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Guru management
            'view_guru',
            'create_guru',
            'edit_guru',
            'delete_guru',
            
            // Siswa management
            'view_siswa',
            'create_siswa',
            'edit_siswa',
            'delete_siswa',
            
            // Kelas management
            'view_kelas',
            'create_kelas',
            'edit_kelas',
            'delete_kelas',
            
            // Mata Pelajaran management
            'view_mata_pelajaran',
            'create_mata_pelajaran',
            'edit_mata_pelajaran',
            'delete_mata_pelajaran',
            
            // Ruangan management
            'view_ruangan',
            'create_ruangan',
            'edit_ruangan',
            'delete_ruangan',
            
            // Jadwal management
            'view_jadwal',
            'create_jadwal',
            'edit_jadwal',
            'delete_jadwal',
            'generate_jadwal',
            
            // Materi management
            'view_materi',
            'create_materi',
            'edit_materi',
            'delete_materi',
            'upload_materi',
            'download_materi',
            
            // Dashboard access
            'access_admin_panel',
            'access_teacher_panel',
            'access_student_frontend',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Guru role - limited access
        $guruRole = Role::create(['name' => 'guru']);
        $guruRole->givePermissionTo([
            'view_jadwal',
            'view_siswa',
            'view_kelas',
            'view_mata_pelajaran',
            'view_materi',
            'create_materi',
            'edit_materi',
            'delete_materi',
            'upload_materi',
            'download_materi',
            'access_teacher_panel',
        ]);

        // Siswa role - very limited access
        $siswaRole = Role::create(['name' => 'siswa']);
        $siswaRole->givePermissionTo([
            'view_jadwal',
            'view_materi',
            'download_materi',
            'access_student_frontend',
        ]);
    }
}
