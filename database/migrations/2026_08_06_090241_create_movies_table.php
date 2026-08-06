<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MovieStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();

            $table->string('title')->index();
            $table->string('original_title')->index()->nullable();
            $table->string('slug')->unique();

            $table->text('synopsis')->nullable();
            $table->unsignedSmallInteger('release_year')->index();
            $table->date('release_date')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();

            $table->enum('status', MovieStatus::cases())->default(MovieStatus::Draft);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
