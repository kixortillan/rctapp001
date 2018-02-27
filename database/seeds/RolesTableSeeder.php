<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Models\Core\Role::class)
            ->create([
                'name' => 'super user',
                'desc' => 'Full access rights administrator.',
                'is_admin' => true,
            ]);

        factory(App\Models\Core\Role::class)
            ->create([
                'name' => 'employee',
                'desc' => 'Minimal access rights. Default role.',
            ]);

    }
}
