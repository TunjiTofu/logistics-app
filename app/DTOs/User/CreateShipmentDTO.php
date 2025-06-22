<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;
use App\Http\Requests\Auth\UserSignupRequest;
use App\Http\Requests\User\Shipment\CreateShipmentRequest;
use App\Models\User;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateShipmentDTO extends BaseDTO
{
    public string $senderName;
    public string $receiverName;
    public string $originAddress;
    public string $destinationAddress;
    public ?float $originLatitude = null;
    public ?float $originLongitude = null;
    public ?float $destinationLatitude = null;
    public ?float $destinationLongitude = null;
    public User $createdBy;

    /**
     * @throws UnknownProperties
     */
    public static function fromRequest(CreateShipmentRequest $request): self
    {
        $data = $request->validated();
        $data['createdBy'] = $request->user();
        return new static($data);
    }

    public function toShipmentData(): array
    {
        return [
            'sender_name' => $this->senderName,
            'receiver_name' => $this->receiverName,
            'origin_address' => $this->originAddress,
            'destination_address' => $this->destinationAddress,
            'origin_latitude' => $this->originLatitude,
            'origin_longitude' => $this->originLongitude,
            'destination_latitude' => $this->destinationLatitude,
            'destination_longitude' => $this->destinationLongitude,
            'created_by' => $this->createdBy->id,
        ];
    }
}
