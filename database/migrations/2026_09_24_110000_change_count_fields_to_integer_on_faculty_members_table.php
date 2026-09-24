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
        Schema::table('faculty_members', function (Blueprint $table) {
            $table->unsignedInteger('projects_ongoing')->nullable()->change();
            $table->unsignedInteger('projects_completed')->nullable()->change();
            $table->unsignedInteger('supervision_phd')->nullable()->change();
            $table->unsignedInteger('supervision_mphil_ms_msc')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_members', function (Blueprint $table) {
            $table->text('projects_ongoing')->nullable()->change();
            $table->text('projects_completed')->nullable()->change();
            $table->text('supervision_phd')->nullable()->change();
            $table->text('supervision_mphil_ms_msc')->nullable()->change();
        });
    }
};
