<?php

namespace App\Repository;

use InvalidArgumentException;
use PDO;

class PostRepository
{
    public const PAGE_SIZE = 6;

    public function __construct(
        private PDO $pdo,
    ) {}

    /**
     * @return array<int, list<array{
     *     category_id: int,
     *     id: int,
     *     title: string,
     *     description: string,
     *     image_path: string,
     *     views: int,
     *     published_at: string,
     *     row_num: int
     * }>>
     */
    public function findForEachCategory(): array
    {
        $sql = '
            SELECT *
            FROM (
                SELECT
                    post_category.category_id,
                    posts.id,
                    posts.title,
                    posts.description,
                    posts.image_path,
                    posts.views,
                    posts.published_at,
                    ROW_NUMBER() OVER(
                        PARTITION BY post_category.category_id
                        ORDER BY posts.published_at DESC, posts.id DESC
                    ) AS row_num
                FROM posts
                JOIN post_category ON  post_category.post_id = posts.id
            ) AS ranked_posts
            WHERE row_num <= 3
            ORDER BY category_id ASC, row_num ASC;
        ';

        $results = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $postsByCategory = [];

        foreach ($results as $post) {
            $postsByCategory[$post['category_id']][] = $post;
        }

        return $postsByCategory;
    }

    public function countByCategory(int $categoryId): int
    {
        $prepared = $this->pdo->prepare('SELECT COUNT(*) FROM post_category WHERE category_id = :category_id');
        $prepared->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $prepared->execute();

        return (int) $prepared->fetchColumn();
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     image_path: string,
     *     views: int,
     *     published_at: string
     * }>
     */
    public function findByCategory(int $categoryId, int $page = 1, string $sort = 'views'): array
    {
        $offset = ($page - 1) * self::PAGE_SIZE;

        $orderBy = match ($sort) {
            'views' => 'posts.views DESC, posts.id DESC',
            'date_desc' => 'posts.published_at DESC, posts.id DESC',
            'date_asc' => 'posts.published_at ASC, posts.id ASC',
            default => throw new InvalidArgumentException('Invalid sort mode: ' . $sort),
        };

        $sql = '
            SELECT
                posts.id,
                posts.title,
                posts.description,
                posts.image_path,
                posts.views,
                posts.published_at
            FROM posts
            JOIN post_category ON post_category.post_id = posts.id
            WHERE post_category.category_id = :category_id
            ORDER BY ' . $orderBy . '
            LIMIT :limit OFFSET :offset
        ';

        $prepared = $this->pdo->prepare($sql);
        $prepared->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $prepared->bindValue('limit', self::PAGE_SIZE, PDO::PARAM_INT);
        $prepared->bindValue('offset', $offset, PDO::PARAM_INT);
        $prepared->execute();

        return $prepared->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return null|array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     body: string,
     *     image_path: string,
     *     views: int,
     *     published_at: string
     * }
     */
    public function findById(int $id): ?array
    {
        $sql = '
            SELECT
                id,
                title,
                description,
                body,
                image_path,
                views,
                published_at
            FROM posts
            WHERE id = :id
        ';

        $prepared = $this->pdo->prepare($sql);
        $prepared->bindValue('id', $id, PDO::PARAM_INT);
        $prepared->execute();

        $post = $prepared->fetch(PDO::FETCH_ASSOC);

        return false === $post ? null : $post;
    }

    public function incrementViews(int $id): void
    {
        $prepared = $this->pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $prepared->bindValue('id', $id, PDO::PARAM_INT);
        $prepared->execute();
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     image_path: string,
     *     views: int,
     *     published_at: string
     * }>
     */
    public function findRelated(int $postId): array
    {
        $sql = '
            SELECT
                posts.id,
                posts.title,
                posts.description,
                posts.image_path,
                posts.views,
                posts.published_at
            FROM posts
            JOIN (
                SELECT
                    post_category.post_id,
                    COUNT(*) AS common_categories_count
                FROM post_category
                JOIN post_category AS current_categories
                    ON current_categories.category_id = post_category.category_id
                WHERE current_categories.post_id = :post_id
                    AND post_category.post_id != current_categories.post_id
                GROUP BY post_category.post_id
            ) AS related_posts ON related_posts.post_id = posts.id
            ORDER BY related_posts.common_categories_count DESC, posts.published_at DESC, posts.id DESC
            LIMIT 3
        ';

        $prepared = $this->pdo->prepare($sql);
        $prepared->bindValue('post_id', $postId, PDO::PARAM_INT);
        $prepared->execute();

        return $prepared->fetchAll(PDO::FETCH_ASSOC);
    }
}
