<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('galleries')->where('status', 'published')->update(['status' => '1']);
        DB::table('galleries')->where('status', 'draft')->update(['status' => '0']);

        DB::statement('ALTER TABLE galleries MODIFY status TINYINT(1) NOT NULL DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE galleries MODIFY status VARCHAR(255) NOT NULL DEFAULT 'draft'");

        DB::table('galleries')->where('status', '1')->update(['status' => 'published']);
        DB::table('galleries')->where('status', '0')->update(['status' => 'draft']);
    }
};
