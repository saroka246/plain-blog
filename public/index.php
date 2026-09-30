<?php

declare(strict_types=1);

use App\Http\Application;

$container = require dirname(__DIR__) . '/config/boostrap.php';

$container->get(Application::class)->run();
