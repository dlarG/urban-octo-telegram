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
        Schema::create('boarding_houses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();

            $t->string('name');
            $t->text('description')->nullable();

            $t->string('address_line')->nullable();
            $t->string('barangay')->nullable();
            $t->string('city')->default('Sogod');
            $t->string('province')->default('Southern Leyte');

            $t->decimal('lat', 10, 7);
            $t->decimal('lng', 10, 7);

            $t->time('curfew_time')->nullable();
            $t->boolean('allows_cooking')->default(false);
            $t->string('gender_policy', 20)->default('mixed'); // male_only / female_only / mixed
            $t->unsignedTinyInteger('water_supply_rating')->nullable(); // 1-5
            $t->boolean('is_sub_metered')->default(false);

            $t->string('status', 30)->default('pending_review')->index(); // PropertyStatus
            $t->text('rejection_reason')->nullable();
            $t->text('suspension_reason')->nullable();
            $t->foreignId('status_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('status_updated_at')->nullable();

            $t->timestamps();
            $t->index(['lat', 'lng']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boarding_houses');
    }
};
