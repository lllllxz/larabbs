<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        // 需清除缓存，否则会报错
//        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
//
//        // 先创建权限
//        \Spatie\Permission\Models\Permission::create(['name' => 'manage_contents']);
//        \Spatie\Permission\Models\Permission::create(['name' => 'manage_users']);
//        \Spatie\Permission\Models\Permission::create(['name' => 'edit_settings']);
//
//        // 创建站长角色，并赋予权限
//        $founder = \Spatie\Permission\Models\Role::create(['name' => 'Founder']);
//        $founder->givePermissionTo('manage_contents');
//        $founder->givePermissionTo('manage_users');
//        $founder->givePermissionTo('edit_settings');
//
//        $maintainer = \Spatie\Permission\Models\Role::create(['name' => 'Maintainer']);
//        $maintainer->givePermissionTo('manage_contents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 需清除缓存，否则会报错
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        \Illuminate\Database\Eloquent\Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        \Illuminate\Database\Eloquent\Model::reguard();
    }
}
