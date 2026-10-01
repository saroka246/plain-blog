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

    /**
     * @return null|array{id: int, name: string, description: string}
     */
    public function findById(int $id): ?array
    {
        $prepared = $this->pdo->prepare('SELECT id, name, description FROM categories WHERE id = :id');
        $prepared->bindValue('id', $id, PDO::PARAM_INT);
        $prepared->execute();

        $category = $prepared->fetch(PDO::FETCH_ASSOC);

        return false === $category ? null : $category;
    }
}
