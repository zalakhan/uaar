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
        DB::statement('ALTER TABLE tenders MODIFY title LONGTEXT NULL');
        DB::statement('ALTER TABLE tenders MODIFY description LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tenders MODIFY title VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE tenders MODIFY description TEXT NULL');
    }
};
