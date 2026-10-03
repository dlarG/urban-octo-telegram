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
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenancy_id')->constrained()->cascadeOnDelete();

            $t->decimal('amount', 10, 2);
            $t->date('due_date');
            $t->date('paid_at')->nullable();
            $t->string('status', 20)->default('pending')->index();

            $t->string('reference')->nullable(); // GCash/Maya ref
            $t->text('notes')->nullable();

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
