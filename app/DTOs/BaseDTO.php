<?php

namespace App\DTOs;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

abstract class BaseDTO extends DataTransferObject
{
    /**
     * @param array $data
     * @return static
     * @throws UnknownProperties
     */
    public static function createFromRequest(array $data): static
    {
        return new static($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
