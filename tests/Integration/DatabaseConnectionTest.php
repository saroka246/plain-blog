<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Database\DBConnectionFactory;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DBConnectionFactory::class)]
#[Group('integration')]
#[Group('database')]
final class DatabaseConnectionTest extends TestCase
{
    public function testConfiguredConnectionCanExecuteAQuery(): void
    {
        $container = require dirname(__DIR__, 2) . '/config/boostrap.php';
        $connection = $container->get(PDO::class);
        $statement = $connection->query('SELECT 1 AS result');

        self::assertNotFalse($statement);
        self::assertSame(['result' => 1], $statement->fetch());
    }
}
