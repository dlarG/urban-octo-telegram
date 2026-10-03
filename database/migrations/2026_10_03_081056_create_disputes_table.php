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
        Schema::create('disputes', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // renter filing
        $t->foreignId('trust_score_event_id')->constrained()->cascadeOnDelete();

        $t->text('reason');
        $t->string('status', 20)->default('open')->index();

        $t->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
        $t->timestamp('resolved_at')->nullable();
        $t->text('resolution_note')->nullable();

        $t->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
