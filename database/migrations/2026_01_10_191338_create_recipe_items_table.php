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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipe_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('food_id')
                ->constrained('foods')
                ->restrictOnDelete();

            $table->decimal('qty', 10, 3);
            $table->string('unit', 10); // g, ml, pcs

            $table->unsignedBigInteger('cooking_profile_id')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['recipe_id']);
            $table->index(['food_id']);
            $table->index(['cooking_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
