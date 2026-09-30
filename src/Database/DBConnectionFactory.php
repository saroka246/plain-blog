<?php

declare(strict_types=1);

namespace App\Database;

use InvalidArgumentException;
use PDO;

class DBConnectionFactory
{
    public function create(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password,
    ): PDO {
        $validatedPort = filter_var($port, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);

        if (false === $validatedPort) {
            throw new InvalidArgumentException('Invalid port');
        }

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $validatedPort, $database);

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
