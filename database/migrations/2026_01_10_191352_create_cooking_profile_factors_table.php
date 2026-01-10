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
        Schema::create('cooking_profile_factors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cooking_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nutrient_key', 50);

            $table->decimal('factor', 6, 4);

            $table->timestamps();

            $table->unique(['cooking_profile_id', 'nutrient_key'], 'uniq_profile_nutrient');
            $table->index(['nutrient_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooking_profile_factors');
    }
};
