<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // Firstly we are creating roles
        $this->call(RoleSeeder::class);

        // And then we are initializing first user and assign him admin role
        $this->call(UserSeeder::class);
    }
}
