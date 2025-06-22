<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Common\PaginatedResourceCollection;

class UserCollection extends PaginatedResourceCollection
{
     /**
     * The key for the resource array.
     *
     * @var string
     */
    protected string $resourceKey = 'users';
}
