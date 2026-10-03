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
        Schema::create('rental_applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('renter_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('room_id')->constrained()->cascadeOnDelete();

            $t->string('status', 20)->default('submitted')->index();
            $t->text('message')->nullable();
            $t->text('response_message')->nullable();
            $t->timestamp('viewed_at')->nullable();
            $t->timestamp('responded_at')->nullable();

            $t->timestamps();

            // Per-room duplicate guard (application-level also enforced in service)
            $t->index(['renter_id', 'room_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_applications');
    }
};
