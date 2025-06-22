<?php

namespace App\Http\Resources\Utility;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedResourceCollection extends ResourceCollection
{
    protected string $resourceKey = 'data';

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->resourceKey => $this->collection,
            'pagination' => [
                'page' => $this->currentPage(),
                'limit' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }

    /**
     * Customize the key for the resource array.
     *
     * @param  string  $key
     * @return $this
     */
    public function withResourceKey(string $key): static
    {
        $this->resourceKey = $key;

        return $this;
    }
}
