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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name', 200);

            $table->decimal('yield_qty', 10, 3);
            $table->string('yield_unit', 10); // g sau ml

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['patient_id']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
