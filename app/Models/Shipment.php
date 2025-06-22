<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        // Generate tracking number before a shipment record is created in the database
        static::creating(function ($shipment) {
            $shipment->tracking_number = '1Z' . strtoupper(Str::random(10));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
