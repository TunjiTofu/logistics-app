<?php

namespace App\Http\Resources\Shipment;


use App\Http\Resources\Utility\PaginatedResourceCollection;

class ShipmentCollection extends PaginatedResourceCollection
{
     /**
     * The key for the resource array.
     *
     * @var string
     */
    protected string $resourceKey = 'shipments';
}
