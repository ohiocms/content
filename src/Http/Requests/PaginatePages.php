<?php
namespace Ohio\Content\Http\Requests;

use Ohio\Core\Http\Requests\PaginateRequest;

class PaginatePages extends PaginateRequest
{
    public $perPage = 10;

    public $orderBy = 'pages.id';

    public $sortable = [
        'pages.id',
        'pages.name',
    ];

    public $searchable = [
        'pages.name',
    ];

}