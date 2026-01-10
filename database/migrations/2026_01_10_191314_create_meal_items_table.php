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
        Schema::create('meal_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_meal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('item_type', 20);

            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('recipe_id')->nullable();

            $table->decimal('portion_qty', 10, 3);
            $table->string('portion_unit', 10);

            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['menu_meal_id', 'sort_order']);
            $table->index(['item_type']);
            $table->index(['food_id']);
            $table->index(['recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_items');
    }
};
