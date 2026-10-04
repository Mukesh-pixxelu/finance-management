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
        if (Schema::hasColumn('savings', 'maturity_notified_at')) {
            return;
        }

        Schema::table('savings', function (Blueprint $table) {
            $table->timestamp('maturity_notified_at')->nullable()->after('interest_earned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('savings', 'maturity_notified_at')) {
            return;
        }

        Schema::table('savings', function (Blueprint $table) {
            $table->dropColumn('maturity_notified_at');
        });
    }
};
