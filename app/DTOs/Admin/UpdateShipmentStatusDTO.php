<?php

namespace App\DTOs\Admin;

use App\DTOs\BaseDTO;
use App\Enums\ShipmentStatusEnum;
use App\Http\Requests\Admin\Shipment\UpdateShipmentStatusRequest;
use App\Models\User;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpdateShipmentStatusDTO extends BaseDTO
{
    public ShipmentStatusEnum $status;
    public User $updatedBy;


    /**
     * @throws UnknownProperties
     */
    public static function fromRequest(UpdateShipmentStatusRequest $request): self
    {
        $data = [
            'status' => ShipmentStatusEnum::from($request->validated()['status']),
            'updatedBy' => $request->user(),
        ];
        return new static($data);
    }

    public function toShipmentUpdateStatusData(): array
    {
        return [
            'status' => $this->status->value,
            'update_by' => $this->updatedBy->id,
        ];
    }
}
