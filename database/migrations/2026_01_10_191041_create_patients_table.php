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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('first_name', 200);
            $table->string('last_name', 200);
            $table->string('sex', 10)->nullable();
            $table->date('birthdate')->nullable();

            $table->unsignedSmallInteger('current_height_cm')->nullable();
            $table->decimal('current_weight_kg', 6, 2)->nullable();

            $table->string('diagnosis')->nullable();

            $table->unsignedSmallInteger('target_kcal_per_day')->nullable();
            $table->decimal('target_protein_g_per_day', 7, 2)->nullable();
            $table->decimal('target_carbs_g_per_day', 7, 2)->nullable();
            $table->decimal('target_fat_g_per_day', 7, 2)->nullable();

            $table->unsignedInteger('limit_sodium_mg_per_day')->nullable();
            $table->unsignedInteger('limit_potassium_mg_per_day')->nullable();
            $table->unsignedInteger('limit_phosphorus_mg_per_day')->nullable();

            $table->unsignedInteger('limit_fluids_ml_per_day')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
