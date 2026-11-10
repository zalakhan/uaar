<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Event registration management tables.
     */
    public function up(): void
    {
        Schema::create('registration_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->unique();
            $table->string('label');
            $table->string('field_type', 32);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->date('event_date')->nullable();
            $table->dateTime('registration_deadline')->nullable();
            $table->string('venue')->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('public_slug', 64)->unique();
            $table->boolean('fees_enabled')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('event_participant_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });

        Schema::create('event_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_field_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field_key');
            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->string('field_type', 32);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'field_key']);
        });

        Schema::create('event_field_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_participant_category_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(
                ['event_form_field_id', 'event_participant_category_id'],
                'event_field_category_unique'
            );
        });

        Schema::create('event_fee_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_participant_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'event_participant_category_id'], 'event_fee_category_unique');
        });

        Schema::create('event_registration_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_participant_category_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('category_label');
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['event_id', 'submitted_at']);
        });

        Schema::create('event_registration_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_form_field_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field_key');
            $table->string('label');
            $table->string('field_type', 32);
            $table->longText('value')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->index(['event_registration_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registration_answers');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_registration_access');
        Schema::dropIfExists('event_fee_rules');
        Schema::dropIfExists('event_field_conditions');
        Schema::dropIfExists('event_form_fields');
        Schema::dropIfExists('event_participant_categories');
        Schema::dropIfExists('events');
        Schema::dropIfExists('registration_fields');
    }
};
