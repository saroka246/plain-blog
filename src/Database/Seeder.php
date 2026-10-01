<?php

namespace App\Database;

use Faker\Generator;
use PDO;
use RuntimeException;
use Throwable;

class Seeder
{
    public function __construct(
        private PDO $pdo,
        private Generator $faker,
    ) {}

    public function run(bool $reset = false): void
    {
        if (!$reset) {
            $this->assertTablesAreEmpty();
        }

        $imagePaths = $this->generateImages(50);

        $this->pdo->beginTransaction();

        try {
            if ($reset) {
                $this->clearTables();
            }

            $categoryIds = $this->seedCategories();
            $postIds = $this->seedPosts($imagePaths);
            $this->seedPostCategories($categoryIds, $postIds);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    private function assertTablesAreEmpty(): void
    {
        $sql = '
            SELECT
                EXISTS(SELECT 1 FROM categories)
                OR EXISTS(SELECT 1 FROM posts)
                OR EXISTS(SELECT 1 FROM post_category) AS has_data
            ';

        if ($this->pdo->query($sql)->fetchColumn()) {
            throw new RuntimeException('Tables are not empty');
        }
    }

    private function clearTables(): void
    {
        $this->pdo->exec('DELETE FROM post_category;');
        $this->pdo->exec('DELETE FROM categories;');
        $this->pdo->exec('DELETE FROM posts;');
    }

    /**
     * @return list<int>
     */
    private function seedCategories(): array
    {
        $categoryCount = 7;
        $parameters = [];

        for ($index = 0; $index < $categoryCount; ++$index) {
            $name = $this->faker->unique()->word();
            $parameters[] = $name;
            $parameters[] = $this->faker->sentence(8);
        }

        $values = implode(', ', array_fill(0, $categoryCount, '(?, ?)'));
        $insert = $this->pdo->prepare('INSERT INTO categories (name, description) VALUES ' . $values);
        $insert->execute($parameters);

        $categoryIds = $this->pdo->query('SELECT id FROM categories ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);

        return array_map('intval', $categoryIds);
    }

    /**
     * @param list<string> $imagePaths
     *
     * @return list<int>
     */
    private function seedPosts(array $imagePaths): array
    {
        if ([] === $imagePaths) {
            throw new RuntimeException('Empty image paths');
        }

        $postCount = 50;
        $parameters = [];

        for ($index = 0; $index < $postCount; ++$index) {
            $views = $this->faker->numberBetween(0, 999);
            $publishedAt = $this->faker->dateTimeBetween(
                '2026-01-01 00:00:00 UTC',
                '2026-09-30 23:59:59 UTC',
                'UTC',
            )->format('Y-m-d H:i:s');

            $parameters[] = $this->faker->sentence(8);
            $parameters[] = $this->faker->sentence(8);
            $parameters[] = $this->faker->paragraphs(5, true);
            $parameters[] = $imagePaths[$index];
            $parameters[] = $views;
            $parameters[] = $publishedAt;
        }

        $values = implode(', ', array_fill(0, $postCount, '(?, ?, ?, ?, ?, ?)'));
        $insert = $this->pdo->prepare(
            'INSERT INTO posts (title, description, body, image_path, views, published_at) VALUES ' . $values,
        );
        $insert->execute($parameters);

        $postIds = $this->pdo->query('SELECT id FROM posts ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);

        return array_map('intval', $postIds);
    }

    /**
     * @param list<int> $categoryIds
     * @param list<int> $postIds
     */
    private function seedPostCategories(array $categoryIds, array $postIds): void
    {
        $distribution = [30, 10, 4, 3, 2, 1, 0];
        $links = [];
        $postIndex = 0;

        foreach ($distribution as $categoryIndex => $postCount) {
            for ($index = 0; $index < $postCount; ++$index) {
                $links[] = [$postIds[$postIndex], $categoryIds[$categoryIndex]];
                ++$postIndex;
            }
        }

        for ($postNumber = 5; $postNumber <= 30; $postNumber += 5) {
            $links[] = [$postIds[$postNumber - 1], $categoryIds[1]];

            if (0 === $postNumber % 10) {
                $links[] = [$postIds[$postNumber - 1], $categoryIds[2]];
            }
        }

        $values = implode(', ', array_fill(0, count($links), '(?, ?)'));
        $insert = $this->pdo->prepare('INSERT INTO post_category (post_id, category_id) VALUES ' . $values);
        $insert->execute(array_merge(...$links));
    }

    /**
     * @return list<string>
     */
    private function generateImages(int $count): array
    {
        if ($count < 1) {
            throw new RuntimeException('Image count must be positive');
        }

        $relativeDirectory = 'assets/images';
        $directory = dirname(__DIR__, 2) . '/public/' . $relativeDirectory;

        if (!is_dir($directory) && !mkdir($directory, 0o775, true)) {
            throw new RuntimeException('Cannot create image directory: ' . $directory);
        }

        $imagePaths = [];

        for ($index = 1; $index <= $count; ++$index) {
            $color = $this->faker->hexColor();
            $filename = 'image-' . $index . '.svg';
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450">'
                . '<rect width="800" height="450" fill="' . $color . '"/>'
                . '<rect x="200" y="175" width="400" height="100" rx="12" fill="#fff"/>'
                . '<text x="400" y="225" text-anchor="middle" dominant-baseline="middle"'
                . ' font-family="sans-serif" font-size="40" fill="#222">Image ' . $index . '</text>'
                . '</svg>';

            $path = $directory . '/' . $filename;

            if (strlen($svg) !== file_put_contents($path, $svg)) {
                throw new RuntimeException('Cannot write image: ' . $path);
            }

            $imagePaths[] = $relativeDirectory . '/' . $filename;
        }

        return $imagePaths;
    }
}
