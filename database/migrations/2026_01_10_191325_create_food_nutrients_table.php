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
        Schema::create('food_nutrients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('food_id')
                ->constrained('foods')
                ->cascadeOnDelete();

            $table->decimal('basis_qty', 8, 2)->default(100);
            $table->string('basis_unit', 10)->default('g');

            $table->decimal('kcal', 8, 2)->nullable();
            $table->decimal('protein_g', 8, 2)->nullable();
            $table->decimal('fat_g', 8, 2)->nullable();
            $table->decimal('carb_g', 8, 2)->nullable();
            $table->decimal('fiber_g', 8, 2)->nullable();

            $table->decimal('sodium_mg', 10, 2)->nullable();
            $table->decimal('potassium_mg', 10, 2)->nullable();
            $table->decimal('phosphorus_mg', 10, 2)->nullable();

            $table->decimal('calcium_mg', 10, 2)->nullable();
            $table->decimal('magnesium_mg', 10, 2)->nullable();
            $table->decimal('iron_mg', 10, 2)->nullable();

            $table->timestamps();

            $table->unique(['food_id', 'basis_unit', 'basis_qty'], 'uniq_food_basis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_nutrients');
    }
};
