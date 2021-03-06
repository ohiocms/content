<?php

return [
    'default_engine' => env('SCOUT_DRIVER', 'local'),
    'classes' => [
        \Belt\Content\Page::class => \Belt\Content\Http\Requests\PaginatePages::class,
        \Belt\Content\Post::class => \Belt\Content\Http\Requests\PaginatePosts::class,
    ]
];