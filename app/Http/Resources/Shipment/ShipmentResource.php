<?php

namespace App\Http\Resources\Shipment;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trackingNumber' => $this->tracking_number,
            'senderName' => $this->sender_name,
            'receiverName' => $this->receiver_name,
            'originAddress' => $this->origin_address,
            'destinationAddress' => $this->destination_address,
            'originLatitude' => $this->origin_latitude,
            'originLongitude' => $this->origin_longitude,
            'destinationLatitude' => $this->destination_latitude,
            'destinationLongitude' => $this->destination_longitude,
            'status' => $this->status,
            'createdBy' => UserResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
