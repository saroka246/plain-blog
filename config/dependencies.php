<?php

declare(strict_types=1);

use App\Database\DBConnectionFactory;
use App\Http\Router;
use Smarty\Smarty;

use function DI\create;
use function DI\env;
use function DI\factory;

$rootDir = dirname(__DIR__);

return [
    PDO::class => factory([DBConnectionFactory::class, 'create'])
        ->parameter('host', env('DB_HOST'))
        ->parameter('port', env('DB_PORT'))
        ->parameter('database', env('DB_DATABASE'))
        ->parameter('username', env('DB_USERNAME'))
        ->parameter('password', env('DB_PASSWORD')),
    Smarty::class => static function () use ($rootDir): Smarty {
        $smarty = new Smarty();
        $smarty->setTemplateDir($rootDir . '/templates');
        $smarty->setCompileDir($rootDir . '/var/smarty/compile');
        $smarty->setEscapeHtml(true);
        $smarty->setCaching(Smarty::CACHING_OFF);

        return $smarty;
    },
    Router::class => create(Router::class)
        ->constructor(require $rootDir . '/config/routes.php'),
];
