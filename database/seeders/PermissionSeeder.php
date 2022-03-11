<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $microPermissions = ['can access', 'settings'];

        $modules = ['class', 'live class', 'test series', 'sale', 'users', 'roles' ];

        // $accessPoints = ['access', 'create', 'edit', 'delete' , 'export'];
        $accessPoints = ['access', 'create', 'edit' ];
        // create permissions
        foreach ($modules as $key => $module) {
            foreach ($accessPoints as $key => $points) {
                Permission::create(['name' => "{$points} {$module}"]);
            }
        }

        foreach ($microPermissions as $key => $permission) {
            Permission::create(['name' => $permission]);
        }

        // admin role
        // $role = Role::create(['name' => 'Super-Admin']);
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $user = User::whereId(2)->first();
        $user->assignRole('super-admin');

        $role = Role::create(['name' => 'customer']);
    }
}
