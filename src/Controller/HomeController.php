<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Smarty\Smarty;

class HomeController
{
    public function __construct(
        private Smarty $smarty,
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
    ) {}

    public function index(): void
    {
        $posts = [];

        $categories = $this->categoryRepository->findNonEmpty();
        if ([] !== $categories) {
            $posts = $this->postRepository->findForEachCategory();
        }

        $this->smarty->assign([
            'title' => 'Главная',
            'categories' => $categories,
            'posts' => $posts,
        ]);

        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/home.tpl');
    }
}
