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
        Schema::create('meal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedSmallInteger('default_sort_order')->default(0);
            $table->boolean('is_default')->default(false); // preset-uri default
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_default');
        });

        // Adaugă coloana meal_type_id în menu_meals
        Schema::table('menu_meals', function (Blueprint $table) {
            $table->foreignId('meal_type_id')->nullable()->after('menu_day_id')->constrained('meal_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_meals', function (Blueprint $table) {
            $table->dropForeign(['meal_type_id']);
            $table->dropColumn('meal_type_id');
        });

        Schema::dropIfExists('meal_types');
    }
};