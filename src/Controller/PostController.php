<?php

namespace App\Controller;

use Smarty\Smarty;

class PostController
{
    public function __construct(
        private Smarty $smarty,
    ) {}

    public function show(string $id): void
    {
        $this->smarty->assign('title', 'Posts');

        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/post.tpl');
    }
}
