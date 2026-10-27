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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->string('name', 191);
            $table->string('icon', 191)->nullable();
            $table->string('url', 191)->default('#');
            $table->string('slug', 191)->default('#');
            $table->string('current', 191)->default('#');
            $table->integer('priority')->nullable();
            $table->json('roles')->nullable()->default('[]'); // roles permitidos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
