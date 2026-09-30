<?php

namespace App\Controller;

use Smarty\Smarty;

class HomeController
{
    public function __construct(
        private Smarty $smarty,
    ) {}

    public function index(): void
    {
        $this->smarty->assign('title', 'Главная');

        header('Content-Type: text/html; charset=UTF-8');
        $this->smarty->display('pages/home.tpl');
    }
}
