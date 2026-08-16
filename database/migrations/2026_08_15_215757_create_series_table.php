<?php

use App\Enums\{SeriesPublishStatus, SeriesProductionStatus};
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
        Schema::create('series', function (Blueprint $table) {
            $table->id();

            $table->string('title')->index();
            $table->string('original_title')->index()->nullable();
            $table->string('slug')->unique();

            $table->text('synopsis')->nullable();
            $table->unsignedSmallInteger('release_year')->index();
            $table->date('release_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignId('age_rating_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('original_language_id')->constrained('languages')->restrictOnDelete();

            $table->enum('publish_status', SeriesPublishStatus::cases())->default(SeriesPublishStatus::Draft);
            $table->enum('production_status', SeriesProductionStatus::cases())->default(SeriesProductionStatus::Ongoing);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
