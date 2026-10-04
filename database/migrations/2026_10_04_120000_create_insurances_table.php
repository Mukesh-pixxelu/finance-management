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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('provider');
            $table->string('policy_number');
            $table->string('insured_person');
            $table->decimal('sum_assured', 12, 2);
            $table->decimal('premium_amount', 10, 2);
            $table->string('premium_frequency');
            $table->date('start_date');
            $table->date('expiry_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'policy_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
