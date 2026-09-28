<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Existing rows may have NULL qualification — fill before NOT NULL alter.
        DB::table('faculty_members')
            ->whereNull('qualification')
            ->update(['qualification' => '']);

        DB::statement('ALTER TABLE faculty_members MODIFY qualification LONGTEXT NOT NULL');

        Schema::table('faculty_members', function (Blueprint $table) {
            $table->text('specialization')->nullable()->after('qualification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_members', function (Blueprint $table) {
            $table->dropColumn('specialization');
        });

        DB::statement('ALTER TABLE faculty_members MODIFY qualification VARCHAR(255) NOT NULL');
    }
};
