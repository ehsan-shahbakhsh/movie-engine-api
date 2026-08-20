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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('season_id')->constrained()->cascadeOnDelete();

            $table->unsignedSmallInteger('episode_number');
            $table->string('title')->nullable();
            $table->text('synopsis')->nullable();
            $table->date('air_date')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();

            $table->timestamps();

            $table->unique(['season_id', 'episode_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
