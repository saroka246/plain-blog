<?php

use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Controller\PostController;

return [
    [
        'method' => 'GET',
        'pattern' => '~\A/\z~',
        'handler' => [HomeController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'pattern' => '~\A/category/(?P<id>[1-9][0-9]*)\z~',
        'handler' => [CategoryController::class, 'show'],
    ],
    [
        'method' => 'GET',
        'pattern' => '~\A/post/(?P<id>[1-9][0-9]*)\z~',
        'handler' => [PostController::class, 'show'],
    ],
];
