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
        Schema::create('cooking_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('name', 200);
            // ex: "Crud", "Fiert 1 apa", "Fiert 2 ape", "Clocot + aruncat apa"

            $table->text('description')->nullable();
            // explicație medicală / practică

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active']);
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooking_profiles');
    }
};
