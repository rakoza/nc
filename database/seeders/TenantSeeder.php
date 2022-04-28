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
            'name' => 'Tenant 1',
            'domain' => 'tenant1.local',
            'email' => 'tenant1@example.com',
            'is_active' => true,
            'src' => 'hr.1.0',
            'db_host' => config('tenants.db_host'),
            'db_username' => 'tenant1',
            'db_password' => 'pass1',
            'redis_host' => config('tenants.redis_host'),
            'timezone' => config('tenants.timezone'),
        ]);

        \DB::table('tenants')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'name' => 'Tenant 2',
            'domain' => 'tenant2.local',
            'email' => 'tenant2@example.com',
            'is_active' => true,
            'src' => 'hr.1.0',
            'db_host' => config('tenants.db_host'),
            'db_username' => 'tenant2',
            'db_password' => 'pass2',
            'redis_host' => config('tenants.redis_host'),
            'timezone' => config('tenants.timezone'),
        ]);

        \DB::table('tenants')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'name' => 'Tenant 3',
            'domain' => 'tenant3.local',
            'email' => 'tenant3@example.com',
            'is_active' => true,
            'src' => 'hr.1.0',
            'db_host' => config('tenants.db_host'),
            'db_username' => 'tenant3',
            'db_password' => 'pass3',
            'redis_host' => config('tenants.redis_host'),
            'timezone' => config('tenants.timezone'),
        ]);
    }
}
