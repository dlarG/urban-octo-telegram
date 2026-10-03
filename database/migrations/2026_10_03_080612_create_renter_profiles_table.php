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
        Schema::create('renter_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $t->string('renter_type', 20); // RenterType

            // student-only
            $t->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $t->string('major')->nullable();
            $t->string('year_level')->nullable();

            // worker-only
            $t->string('occupation')->nullable();
            $t->string('employer')->nullable();

            // tourist-only
            $t->string('stay_duration')->nullable();

            $t->decimal('budget_min', 10, 2)->nullable();
            $t->decimal('budget_max', 10, 2)->nullable();

            $t->string('valid_id_path')->nullable();
            $t->timestamp('valid_id_submitted_at')->nullable();

            $t->boolean('trust_score_consent')->default(false);

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renter_profiles');
    }
};
