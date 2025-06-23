<?php

namespace App\Http\Resources\Admin;


use App\Http\Resources\Utility\PaginatedResourceCollection;

class SystemLogsCollection extends PaginatedResourceCollection
{
     /**
     * The key for the resource array.
     *
     * @var string
     */
    protected string $resourceKey = 'logs';
}
