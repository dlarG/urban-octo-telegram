<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            // Allowed to be null at registration — filled during onboarding
            $table->string('business_name')->nullable()->change();
            $table->string('valid_id_path')->nullable()->change();
            $table->string('business_permit_path')->nullable()->change();

            // Gate for property listing
            $table->timestamp('onboarding_completed_at')->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('landlord_profiles', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed_at');
        });
    }
};