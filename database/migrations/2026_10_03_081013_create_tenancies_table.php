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
        Schema::create('tenancies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('rental_application_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('renter_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('room_id')->constrained()->restrictOnDelete();
            $t->foreignId('landlord_id')->constrained('users')->restrictOnDelete();

            $t->string('status', 20)->default('active')->index(); // TenancyStatus
            $t->date('start_date');
            $t->date('end_date')->nullable();
            $t->decimal('monthly_rent', 10, 2);

            $t->timestamp('completed_at')->nullable();
            $t->timestamp('terminated_at')->nullable();
            $t->text('termination_reason')->nullable();

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancies');
    }
};
