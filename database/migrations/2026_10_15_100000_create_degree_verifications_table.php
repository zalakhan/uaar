<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('degree_verifications', function (Blueprint $table) {
            $table->increments('scroll_id');
            $table->string('degree_id', 50);
            $table->string('reg_no', 50);
            $table->string('st_name', 255);
            $table->string('st_fname', 255);
            $table->string('degree_name', 255);
            $table->string('subject', 255);
            $table->unsignedTinyInteger('level');
            $table->unsignedSmallInteger('year');

            $table->index(['year', 'level', 'reg_no']);
            $table->index(['year', 'level', 'degree_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('degree_verifications');
    }
};
