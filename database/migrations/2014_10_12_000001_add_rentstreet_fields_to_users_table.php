<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('role', 20)->default('renter')->after('password');
            $table->string('profile_photo_path')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->index()->after('profile_photo_path');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('is_active');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->string('registration_ip', 45)->nullable()->after('locked_until');
            $table->timestamp('last_login_at')->nullable()->after('registration_ip');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'role', 'profile_photo_path', 'is_active',
                'failed_login_attempts', 'locked_until',
                'registration_ip', 'last_login_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};