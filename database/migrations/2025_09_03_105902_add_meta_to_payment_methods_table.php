<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'brand')) {
                $table->string('brand')->nullable()->after('mollie_mandate_id');
            }
            if (!Schema::hasColumn('payment_methods', 'last4')) {
                $table->string('last4', 4)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('payment_methods', 'holder')) {
                $table->string('holder')->nullable()->after('last4');
            }
            if (!Schema::hasColumn('payment_methods', 'method')) {
                $table->string('method')->nullable()->after('holder');
            }
            if (!Schema::hasColumn('payment_methods', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_default')->index();
            }

            // unique constraint (ako već ne postoji)
            $table->unique(['user_id','mollie_mandate_id'], 'uniq_user_mandate');
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (Schema::hasColumn('payment_methods', 'brand')) {
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('payment_methods', 'last4')) {
                $table->dropColumn('last4');
            }
            if (Schema::hasColumn('payment_methods', 'holder')) {
                $table->dropColumn('holder');
            }
            if (Schema::hasColumn('payment_methods', 'method')) {
                $table->dropColumn('method');
            }
            if (Schema::hasColumn('payment_methods', 'is_active')) {
                $table->dropColumn('is_active');
            }

            $table->dropUnique('uniq_user_mandate');
        });
    }
};
