<?php

use App\Models\Core\UserRole;
use Illuminate\Database\Seeder;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(UserRole::class)->create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
