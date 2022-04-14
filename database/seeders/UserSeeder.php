<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'name' => 'Predrag Rakonjac',
            'email' => 'predrag.rakonjac@gmail.com',
            'password' => bcrypt('predrag.rakonjac'),

            // additional fields
            'is_active' => true,
            'role_id' => 1,
            'phone' => '+38267200480',
            'address' => 'Pera Ćetkovića 95, Podgorica',
        ]);
    }
}
