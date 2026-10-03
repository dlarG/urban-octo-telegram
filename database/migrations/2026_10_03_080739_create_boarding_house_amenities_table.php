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
        Schema::create('boarding_house_amenities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boarding_house_id')->constrained()->cascadeOnDelete();
            $t->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['boarding_house_id', 'amenity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boarding_house_amenities');
    }
};
