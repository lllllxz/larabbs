<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersTableSeeder::class);
		$this->call(TopicsTableSeeder::class);
        $this->call(RepliesTableSeeder::class);
        $this->call(LinksTableSeeder::class);
    }
}
