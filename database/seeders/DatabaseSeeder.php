<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Shipment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'role' => UserTypeEnum::ADMIN->value,
        ]);

        // Create regular users
        $users = User::factory(5)->create();

        // Create shipments
        foreach ($users as $user) {
            Shipment::factory(3)->create(['created_by' => $user->id]);
        }

        // Create some shipments for admin
        Shipment::factory(2)->create(['created_by' => $admin->id]);
    }
}
