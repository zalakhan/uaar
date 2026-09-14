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
        Schema::create('campus_publications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['News Letter', 'Campus News']);
            $table->string('duration');
            $table->unsignedSmallInteger('year');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_publications');
    }
};
