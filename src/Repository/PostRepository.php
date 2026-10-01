<?php

namespace App\Repository;

use PDO;

class PostRepository
{
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
}
