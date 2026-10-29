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
        Schema::create('academic_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_level_id')->constrained('education_levels')->onDelete('restrict');
            $table->foreignId('knowledge_area_id')->constrained('knowledge_areas')->onDelete('restrict');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('snies', 10)->unique();
            $table->string('resolution')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_programs');
    }
};
