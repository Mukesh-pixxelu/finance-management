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
        if (Schema::hasColumn('pensions', 'pension_start_date')) {
            return;
        }

        Schema::table('pensions', function (Blueprint $table) {
            $table->date('pension_start_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pensions', 'pension_start_date')) {
            return;
        }

        Schema::table('pensions', function (Blueprint $table) {
            $table->dropColumn('pension_start_date');
        });
    }
};
