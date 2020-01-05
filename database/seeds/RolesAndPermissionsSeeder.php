<?php

use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 需清除缓存，否则会报错
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 先创建权限
        \Spatie\Permission\Models\Permission::create(['name' => 'manage_contents']);
        \Spatie\Permission\Models\Permission::create(['name' => 'manage_users']);
        \Spatie\Permission\Models\Permission::create(['name' => 'edit_settings']);

        // 创建站长角色，并赋予权限
        $founder = \Spatie\Permission\Models\Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_contents');
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('edit_settings');

        $maintainer = \Spatie\Permission\Models\Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('manage_contents');
    }
}
