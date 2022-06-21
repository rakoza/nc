<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tenants')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'name' => 'T-COM GmbH',
            'domain' => 't-com.nopus.pro',
            'email' => 'tenant1@example.com',
            'is_active' => true,
            'src' => 'nopus',
            'db_host' => config('tenants.db_host'),
            'db_username' => 'tenant1',
            'db_password' => 'pass1',
            'redis_host' => config('tenants.redis_host'),
            'word_to_pdf_worker' => config('tenants.word_to_pdf_worker'),
            'timezone' => config('tenants.timezone'),
        ]);

        \DB::table('tenants')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'name' => 'Twitter Ltd',
            'domain' => 'twitter.nopus.pro',
            'email' => 'tenant2@example.com',
            'is_active' => true,
            'src' => 'nopus',
            'db_host' => config('tenants.db_host'),
            'db_username' => 'tenant2',
            'db_password' => 'pass2',
            'redis_host' => config('tenants.redis_host'),
            'word_to_pdf_worker' => config('tenants.word_to_pdf_worker'),
            'timezone' => config('tenants.timezone'),
        ]);
    }
}
