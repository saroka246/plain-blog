<?php

declare(strict_types=1);

use App\Database\Seeder;

try {
    $container = require dirname(__DIR__) . '/config/boostrap.php';

    $arguments = array_slice($argv, 1);
    $reset = in_array('--reset', $arguments, true);

    $container->get(Seeder::class)->run($reset);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Seeding failed: ' . $exception->getMessage() . PHP_EOL);

    exit(1);
}

fwrite(STDOUT, 'Seeding completed successfully.' . PHP_EOL);

exit(0);
