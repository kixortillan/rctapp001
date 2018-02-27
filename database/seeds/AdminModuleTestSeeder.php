<?php

use Illuminate\Database\Seeder;

class AdminModuleTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admRole = factory(App\Models\Core\Role::class)->create([
            'name' => 'admin',
            'desc' => 'admin role',
            'is_admin' => true,
        ]);

        for ($i = 0; $i < 100; $i++) {

            $faker = Faker\Factory::create();

            $user = factory(App\User::class)->create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'verified' => true,
                'verified_at' => $faker->dateTimeThisDecade(),
                'password' => bcrypt('secret'),
            ]);
            $user->roles()->attach(2);

        }

        for ($i = 0; $i < 100; $i++) {

            $faker = Faker\Factory::create();

            $user = factory(App\User::class)->create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'verified' => true,
                'verified_at' => $faker->dateTimeThisDecade(),
                'password' => bcrypt('secret'),
            ]);
            $user->roles()->attach($admRole->id);

        }

    }
}
