<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage users',
            'manage courses',
            'manage announcements',
            'manage learning resources',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Assign all permissions to admin
        $admin->syncPermissions(Permission::all());

        // Assign some permissions to teacher (example)
        $teacher->givePermissionTo(['manage courses', 'manage learning resources']);
    }
}
