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
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenancy_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('renter_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('boarding_house_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating'); // 1-5
            $t->text('comment')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
