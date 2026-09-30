<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require_once dirname(__DIR__).'/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/dependencies.php');

return $builder->build();
