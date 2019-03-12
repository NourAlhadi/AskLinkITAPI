<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('users')->insert([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@linkit.com',
            'password' => Hash::make('LinkIT'),
        ]);
    }
}
