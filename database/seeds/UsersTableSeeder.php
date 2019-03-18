<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Default Administrator',
            'email' => 'admin@localhost',
            'password' => bcrypt('password'),
            'admin' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Testy McTestface',
            'email' => 'user@localhost',
            'password' => bcrypt('password'),
            'admin' => 0
        ]);
    }
}
