<?php

namespace App\Http;

use RuntimeException;

class Router
{
    /**
     * @param list<array> $routes
     */
    public function __construct(
        private array $routes,
    ) {}

    /**
     * @return array{
     *     handler:null|array{class-string, string},
     *     parameters: array<string, string>,
     *     allowedMethods: list<string>,
     * }
     */
    public function match(string $method, string $path): array
    {
        $resultArray = [
            'handler' => null,
            'parameters' => [],
            'allowedMethods' => [],
        ];
        foreach ($this->routes as $route) {
            $matches = [];
            $result = preg_match($route['pattern'], $path, $matches);

            if (false === $result) {
                throw new RuntimeException('Invalid regexp: ' . $route['pattern']);
            }

            if (0 === $result) {
                continue;
            }

            if ($method != $route['method']) {
                if (!in_array($route['method'], $resultArray['allowedMethods'], true)) {
                    $resultArray['allowedMethods'][] = $route['method'];
                }

                continue;
            }

            $resultArray['handler'] = $route['handler'];

            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $resultArray['parameters'][$key] = $match;
                }
            }

            $resultArray['allowedMethods'] = [];

            break;
        }

        return $resultArray;
    }
}
