<?php

namespace Database\Factories;

use App\Enums\ShipmentStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tracking_number' => Str::uuid()->toString(),
            'sender_name' => fake()->name(),
            'receiver_name' => fake()->name(),
            'origin_address' => fake()->address(),
            'destination_address' => fake()->address(),
            'origin_latitude' => fake()->latitude(),
            'origin_longitude' => fake()->longitude(),
            'destination_latitude' => fake()->latitude(),
            'destination_longitude' => fake()->longitude(),
            'status' => fake()->randomElement([
                ShipmentStatusEnum::PENDING->value,
                ShipmentStatusEnum::IN_TRANSIT->value,
                ShipmentStatusEnum::DELIVERED->value
            ]),
            'created_by' => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShipmentStatusEnum::PENDING->value,
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShipmentStatusEnum::IN_TRANSIT->value,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShipmentStatusEnum::DELIVERED->value,
        ]);
    }
}
