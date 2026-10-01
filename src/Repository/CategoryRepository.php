<?php

namespace App\Repository;

use PDO;

class CategoryRepository
{
    public function __construct(
        private PDO $pdo,
    ) {}

    /**
     * @return list<array{id: int, name: string, description: string}>
     */
    public function findNonEmpty(): array
    {
        $sql = '
            SELECT
                id,
                name,
                description
            FROM categories
            WHERE EXISTS (
                SELECT 1
                FROM post_category
                WHERE category_id = categories.id
            )
            ORDER BY id ASC
        ';

        return $this->pdo->query($sql)->fetchAll();
    }
}
