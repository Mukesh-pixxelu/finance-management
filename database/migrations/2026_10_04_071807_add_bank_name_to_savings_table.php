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
        if (Schema::hasColumn('savings', 'bank_name')) {
            return;
        }

        Schema::table('savings', function (Blueprint $table) {
            $table->string('bank_name')->default('')->after('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('savings', 'bank_name')) {
            return;
        }

        Schema::table('savings', function (Blueprint $table) {
            $table->dropColumn('bank_name');
        });
    }
};
