<?php

return [
    'path' => env('TENANTS_PATH'),
    'db_host' => env('TENANTS_DB_HOST', 'host.docker.internal'),
    'redis_host' => env('TENANTS_REDIS_HOST', 'host.docker.internal'),
    'word_to_pdf_worker' => env('TENANTS_WORD_TO_PDF_WORKER', 'host.docker.internal'),
    'timezone' => env('TENANTS_TIMEZONE', 'UTC'),
];
