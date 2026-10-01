<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Smarty\Smarty;

class PostController
{
    public function __construct(
        private Smarty $smarty,
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
    ) {}

    public function show(string $id): void
    {
        $postId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $post = false === $postId ? null : $this->postRepository->findById($postId);

        if (null !== $post) {
            $this->postRepository->incrementViews($postId);
            $post = $this->postRepository->findById($postId);
        }

        if (null === $post) {
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

        $categories = $this->categoryRepository->findByPost($postId);
        $relatedPosts = $this->postRepository->findRelated($postId);

        $this->smarty->assign([
            'title' => $post['title'],
            'post' => $post,
            'categories' => $categories,
            'relatedPosts' => $relatedPosts,
        ]);

        http_response_code(200);
        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/post.tpl');
    }
}
