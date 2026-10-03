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
        Schema::create('property_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boarding_house_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('room_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->boolean('is_primary')->default(false);
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();

            // At least one parent must be set
            $t->index(['boarding_house_id', 'is_primary']);
            $t->index(['room_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
