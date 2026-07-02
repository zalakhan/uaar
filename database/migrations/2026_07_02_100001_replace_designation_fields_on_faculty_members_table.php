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
            $table->dropColumn(['designation', 'additional_department', 'additional_designation']);
        });

        Schema::table('faculty_members', function (Blueprint $table) {
            $table->foreignId('designation_id')
                ->nullable()
                ->after('name')
                ->constrained('designations', 'designation_id')
                ->nullOnDelete();
            $table->foreignId('additional_designation_id')
                ->nullable()
                ->after('designation_id')
                ->constrained('designations', 'designation_id')
                ->nullOnDelete();
            $table->foreignId('additional_department_id')
                ->nullable()
                ->after('additional_designation_id')
                ->constrained('departments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_members', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['additional_designation_id']);
            $table->dropForeign(['additional_department_id']);
            $table->dropColumn(['designation_id', 'additional_designation_id', 'additional_department_id']);
        });

        Schema::table('faculty_members', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('name');
            $table->string('additional_department')->nullable();
            $table->string('additional_designation')->nullable();
        });
    }
};
