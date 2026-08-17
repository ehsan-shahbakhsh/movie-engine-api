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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('series_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('season_number')->index();
            $table->string('title')->nullable();

            $table->date('release_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();

            $table->unique(['series_id', 'season_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
