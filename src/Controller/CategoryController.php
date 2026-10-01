<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Smarty\Smarty;

class CategoryController
{
    public function __construct(
        private Smarty $smarty,
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
    ) {}

    public function show(string $id): void
    {
        $categoryId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $category = false === $categoryId ? null : $this->categoryRepository->findById($categoryId);

        if (null === $category) {
            http_response_code(404);
            header('Content-Type: text/html; charset=UTF-8');
            $this->smarty->assign([
                'statusCode' => 404,
                'title' => 'Page not found',
                'message' => 'Check the page URL or return to the home page',
            ]);
            $this->smarty->display('pages/error.tpl');

            return;
        }

        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (false === $page) {
            $page = 1;
        }

        $sort = $_GET['sort'] ?? 'views';
        $invalidSortFlag = !in_array($sort, ['views', 'date_desc', 'date_asc'], true);

        if ($invalidSortFlag) {
            $sort = 'views';
        }

        $totalPosts = $this->postRepository->countByCategory($categoryId);
        $totalPages = max(1, (int) ceil($totalPosts / PostRepository::PAGE_SIZE));
        $pageOutOfRangeFlag = $page > $totalPages;

        if ($pageOutOfRangeFlag) {
            $page = 1;
        }

        if ($invalidSortFlag || $pageOutOfRangeFlag) {
            $query = http_build_query(['sort' => $sort, 'page' => $page]);
            header('Location: /category/' . $categoryId . '?' . $query, true, 302);

            return;
        }

        $posts = $totalPosts > 0 ? $this->postRepository->findByCategory($categoryId, $page, $sort) : [];

        $this->smarty->assign([
            'title' => $category['name'],
            'category' => $category,
            'posts' => $posts,
            'sort' => $sort,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts,
        ]);

        http_response_code(200);
        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/category.tpl');
    }
}
