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
        Schema::create('faculty_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained()->nullOnDelete();
            $table->string('member_type')->default('faculty'); // faculty or staff
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('qualification')->nullable();
            $table->text('bio')->nullable();
            $table->text('address')->nullable();
            $table->unsignedSmallInteger('total_experience')->nullable();
            $table->unsignedSmallInteger('total_publication')->nullable();
            $table->boolean('is_hec')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_studyleave')->default(false);
            $table->boolean('is_onleave')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('additional_department')->nullable();
            $table->string('additional_designation')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_members');
    }
};
