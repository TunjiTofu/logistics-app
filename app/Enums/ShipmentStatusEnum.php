<?php

namespace App\Enums;

enum ShipmentStatusEnum: string
{
    case PENDING = 'pending';
    case IN_TRANSIT = 'in-transit';
    case DELIVERED = 'delivered';
}
