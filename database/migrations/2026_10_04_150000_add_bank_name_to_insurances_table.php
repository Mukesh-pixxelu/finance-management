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
        if (Schema::hasColumn('insurances', 'bank_name')) {
            return;
        }

        Schema::table('insurances', function (Blueprint $table) {
            $table->string('bank_name')->default('')->after('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('insurances', 'bank_name')) {
            return;
        }

        Schema::table('insurances', function (Blueprint $table) {
            $table->dropColumn('bank_name');
        });
    }
};
