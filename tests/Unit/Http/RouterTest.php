<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use App\Controller\HomeController;
use App\Controller\PostController;
use App\Http\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Router::class)]
#[Group('unit')]
final class RouterTest extends TestCase
{
    private const ROUTES = [
        ['method' => 'GET', 'pattern' => '~\A/\z~', 'handler' => [HomeController::class, 'index']],
        ['method' => 'GET', 'pattern' => '~\A/post/(?P<id>[1-9][0-9]*)\z~', 'handler' => [PostController::class, 'show']],
    ];

    public function testMatchesStaticRoute(): void
    {
        $router = new Router(self::ROUTES);

        self::assertSame([
            'handler' => [HomeController::class, 'index'],
            'parameters' => [],
            'allowedMethods' => [],
        ], $router->match('GET', '/'));
    }

    public function testExtractsOnlyNamedParametersAsStrings(): void
    {
        $router = new Router(self::ROUTES);

        self::assertSame([
            'handler' => [PostController::class, 'show'],
            'parameters' => ['id' => '42'],
            'allowedMethods' => [],
        ], $router->match('GET', '/post/42'));
    }

    public function testUnknownPathHasNoHandlerOrAllowedMethods(): void
    {
        $router = new Router(self::ROUTES);
        $expected = ['handler' => null, 'parameters' => [], 'allowedMethods' => []];

        self::assertSame($expected, $router->match('GET', '/missing'));
        self::assertSame($expected, $router->match('POST', '/missing'));
    }

    public function testUnsupportedMethodReturnsUniqueAllowedMethods(): void
    {
        $getRoute = self::ROUTES[1];
        $postRoute = $getRoute;
        $postRoute['method'] = 'POST';
        $router = new Router([$getRoute, $postRoute, $getRoute]);

        self::assertSame([
            'handler' => null,
            'parameters' => [],
            'allowedMethods' => ['GET', 'POST'],
        ], $router->match('DELETE', '/post/42'));
    }

    public function testSearchContinuesAfterMethodMismatch(): void
    {
        $getRoute = self::ROUTES[1];
        $postRoute = $getRoute;
        $postRoute['method'] = 'POST';
        $postRoute['handler'] = [HomeController::class, 'index'];
        $router = new Router([$postRoute, $getRoute]);

        self::assertSame([
            'handler' => [PostController::class, 'show'],
            'parameters' => ['id' => '42'],
            'allowedMethods' => [],
        ], $router->match('GET', '/post/42'));
    }

    public function testFirstMatchWinsWithoutMixingParameters(): void
    {
        $router = new Router([
            self::ROUTES[1],
            ['method' => 'GET', 'pattern' => '~\A/post/(?P<slug>[1-9][0-9]*)\z~', 'handler' => [HomeController::class, 'index']],
        ]);

        self::assertSame([
            'handler' => [PostController::class, 'show'],
            'parameters' => ['id' => '42'],
            'allowedMethods' => [],
        ], $router->match('GET', '/post/42'));
    }
}
