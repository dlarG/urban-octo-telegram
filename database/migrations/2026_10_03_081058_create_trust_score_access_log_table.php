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
        Schema::create('trust_score_access_log', function (Blueprint $t) {
            $t->id();
            $t->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('rental_application_id')->constrained()->cascadeOnDelete();
            $t->string('ip_address', 45)->nullable();
            $t->timestamp('viewed_at');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_score_access_log');
    }
};
