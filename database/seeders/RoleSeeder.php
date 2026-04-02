<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder{
public function run(): void
{
    // создаём права
    $permissions = [
        'create tickets',
        'view tickets',
        'delete tickets',
        'update tickets',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    // создаём роль admin
    $adminRole = Role::firstOrCreate(['name' => 'admin']);

    // даём все права
    $adminRole->givePermissionTo(Permission::all());
}
}
