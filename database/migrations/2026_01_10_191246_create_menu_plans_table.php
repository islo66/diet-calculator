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
        Schema::create('menu_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 200);

            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['patient_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_plans');
    }
};
