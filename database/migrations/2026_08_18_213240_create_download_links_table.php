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
        Schema::create('download_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('download_group_id')->constrained()->cascadeOnDelete();

            $table->foreignId('quality_id')->constrained()->restrictOnDelete();
            $table->foreignId('encoder_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('codec_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_links');
    }
};
