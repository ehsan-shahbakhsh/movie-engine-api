<?php

use App\Enums\PersonSeriesDepartment;
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
        Schema::create('person_series', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('series_id')->constrained()->cascadeOnDelete();

            $table->enum('department', PersonSeriesDepartment::cases());
            $table->string('job')->nullable();
            $table->string('character_name')->nullable();

            $table->timestamps();

            $table->unique([
                'person_id',
                'series_id',
                'job'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_series');
    }
};
