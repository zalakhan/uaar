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
            $table->text('research_link')->nullable()->after('bio');
            $table->text('research_group')->nullable()->after('research_link');
            $table->text('projects_ongoing')->nullable()->after('research_group');
            $table->text('projects_completed')->nullable()->after('projects_ongoing');
            $table->text('supervision_phd')->nullable()->after('projects_completed');
            $table->text('supervision_mphil_ms_msc')->nullable()->after('supervision_phd');
            $table->text('patent')->nullable()->after('supervision_mphil_ms_msc');
            $table->text('consultancy_services')->nullable()->after('patent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_members', function (Blueprint $table) {
            $table->dropColumn([
                'research_link',
                'research_group',
                'projects_ongoing',
                'projects_completed',
                'supervision_phd',
                'supervision_mphil_ms_msc',
                'patent',
                'consultancy_services',
            ]);
        });
    }
};
