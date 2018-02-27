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
        //
        factory(App\User::class)->create([
            'first_name' => 'Super',
            'middle_name' => null,
            'last_name' => 'User',
            'username' => 'superuser',
            'email' => null,
            'verified' => true,
            'password' => bcrypt('superuser'),
        ]);
    }
}
