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
        Schema::create('rooms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boarding_house_id')->constrained()->cascadeOnDelete();

            $t->string('room_label'); // "Room 1", "A", etc.
            $t->string('room_type', 20); // RoomType
            $t->unsignedTinyInteger('capacity')->default(1);
            $t->decimal('base_price_monthly', 10, 2);

            $t->boolean('has_own_bathroom')->default(false);
            $t->boolean('has_aircon')->default(false);

            $t->string('status', 20)->default('available')->index();

            $t->timestamps();
            $t->unique(['boarding_house_id', 'room_label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
