<?php

return [
    'path' => env('TENANTS_PATH'),

    'db_host' => env('TENANTS_DB_HOST', 'host.docker.internal'),
    'redis_host' => env('TENANTS_REDIS_HOST', 'host.docker.internal'),
    'timezone' => env('TENANTS_TIMEZONE', 'UTC'),
];
