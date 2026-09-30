<?php

namespace App\Http;

use Psr\Container\ContainerInterface;
use Smarty\Exception;
use Smarty\Smarty;

class Application
{
    public function __construct(
        private Router $router,
        private Smarty $smarty,
        private ContainerInterface $container,
    ) {}

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $path = explode('?', $uri, 2)[0];

        $result = $this->router->match($method, $path);

        if (null === $result['handler']) {
            $this->sendError($result['allowedMethods']);

            return;
        }

        $controller = $this->container->get($result['handler'][0]);

        $controller->{$result['handler'][1]}(...$result['parameters']);
    }

    /**
     * @param list<string> $allowedMethods
     *
     * @throws Exception
     */
    private function sendError(array $allowedMethods): void
    {
        if (!empty($allowedMethods)) {
            $statusCode = 405;
            $title = 'Method not allowed';
            $message = 'The requested HTTP method is not supported for this page';
            header('Allow: ' . implode(', ', $allowedMethods));
        } else {
            $statusCode = 404;
            $title = 'Page not found';
            $message = 'Check the page URL or return to the home page';
        }

        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');

        $this->smarty->assign([
            'statusCode' => $statusCode,
            'title' => $title,
            'message' => $message,
        ]);
        $this->smarty->display('pages/error.tpl');
    }
}
