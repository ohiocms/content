<?php
namespace Ohio\Content\Http\Requests;

use Ohio\Core\Http\Requests\PaginateRequest;

class PaginateCategories extends PaginateRequest
{
    public $perCategory = 10;

    public $orderBy = 'categories.id';

    public $sortable = [
        'categories.id',
        'categories.name',
    ];

    public $searchable = [
        'categories.name',
    ];

}