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
        Schema::create('landlord_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $t->string('business_name');
            $t->string('gcash_number', 20)->nullable();
            $t->string('maya_number', 20)->nullable();

            $t->string('valid_id_path');
            $t->string('business_permit_path');

            $t->string('approval_status', 20)->default('pending')->index();
            $t->text('rejection_reason')->nullable();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('rejected_at')->nullable();
            $t->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_profiles');
    }
};
