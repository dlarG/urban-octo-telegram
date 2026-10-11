<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('renter_profiles', function (Blueprint $t) {
            $t->string('valid_id_hash', 64)->nullable()->index()->after('valid_id_submitted_at');
        });
    }
    public function down(): void {
        Schema::table('renter_profiles', function (Blueprint $t) {
            $t->dropColumn('valid_id_hash');
        });
    }
};
