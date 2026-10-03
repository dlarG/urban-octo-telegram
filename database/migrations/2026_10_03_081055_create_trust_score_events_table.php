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
        Schema::create('trust_score_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tenancy_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('dispute_id')->nullable(); // set later when dispute filed

            $t->string('event_type', 40); // TrustEventType
            $t->decimal('delta', 5, 2);
            $t->decimal('score_after', 5, 2);
            $t->text('reason')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_score_events');
    }
};
