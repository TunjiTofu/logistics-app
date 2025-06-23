<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->text('origin_address');
            $table->text('destination_address');
            $table->decimal('origin_latitude', 10, 6)->nullable();
            $table->decimal('origin_longitude', 10, 6)->nullable();
            $table->decimal('destination_latitude', 10, 6)->nullable();
            $table->decimal('destination_longitude', 10, 6)->nullable();
            $table->enum('status', ['pending', 'in-transit', 'delivered'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Added indexes
            $table->index('status');
            $table->index('created_by');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
