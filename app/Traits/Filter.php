<?php

namespace App\Traits;

trait Filter
{
    public function scopeStatusFilter($query)
    {
        if (request()->filled('status')) {
            return $query->where('status', request()->status);
        }

        return $query;
    }
}
