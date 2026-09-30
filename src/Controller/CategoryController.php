<?php

namespace App\Controller;

use Smarty\Smarty;

class CategoryController
{
    public function __construct(
        private Smarty $smarty,
    ) {}

    public function show(string $id): void
    {
        $this->smarty->assign('title', 'Categories');

        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/category.tpl');
    }
}
