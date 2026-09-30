<?php

declare(strict_types=1);

use App\Database\DBConnectionFactory;

use function DI\env;
use function DI\factory;

return [
    PDO::class => factory([DBConnectionFactory::class, 'create'])
        ->parameter('host', env('DB_HOST'))
        ->parameter('port', env('DB_PORT'))
        ->parameter('database', env('DB_DATABASE'))
        ->parameter('username', env('DB_USERNAME'))
        ->parameter('password', env('DB_PASSWORD')),
];
