<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // teacher model permissions
        $permissions = [
            'viewAny-teacher',
            'view-teacher',
            'create-teacher',
            'update-teacher',
            'delete-teacher',
            'restore-teacher',
            'forceDelete-teacher',
        ];

        // Buat permission jika belum ada
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'sanctum']);
        }

        // Cari atau buat role 'admin'
        // Gunakan 'firstOrCreate' untuk menghindari error jika role 'admin' sudah ada
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);

        // Berikan semua permission kepada role 'admin'
        $adminRole->syncPermissions($permissions); // syncPermissions akan menambahkan yang belum ada dan menghapus yang tidak ada di array
                                                // Jika Anda ingin menambahkan saja tanpa menghapus yang sudah ada, gunakan givePermissionTo
        
    }
}
