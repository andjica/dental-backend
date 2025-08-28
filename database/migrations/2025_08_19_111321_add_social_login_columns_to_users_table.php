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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('password')->index();      // 'google' | 'apple'
            $table->string('provider_id')->nullable()->after('provider')->index();   // sub iz ID tokena
            $table->string('provider_email')->nullable()->after('provider_id');
            $table->string('provider_avatar')->nullable()->after('provider_email');
            $table->string('last_login_provider')->nullable()->after('provider_avatar');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_id',
                'provider_email',
                'provider_avatar',
                'last_login_provider',
            ]);
        });
    }
};
