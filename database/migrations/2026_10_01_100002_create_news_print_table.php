<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_print', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsprint_album_id')->constrained('newsprint_album')->cascadeOnDelete();
            $table->foreignId('newspaper_id')->constrained('newspapers')->restrictOnDelete();
            $table->string('news_file');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_print');
    }
};
