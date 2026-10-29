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
        Schema::create('instrument_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_instrument_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('version_number');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(
                ['evaluation_instrument_id', 'version_number'],
                'instrument_versions_instrument_version_unique',
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instrument_versions');
    }
};
