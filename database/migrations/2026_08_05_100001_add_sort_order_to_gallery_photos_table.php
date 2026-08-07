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
        Schema::table('gallery_photos', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('photo');
        });

        // Assign order to existing photos based on upload sequence.
        DB::table('gallery_photos')->orderBy('gallery_id')->orderBy('id')->get()->groupBy('gallery_id')->each(function ($photos) {
            foreach ($photos->values() as $index => $photo) {
                DB::table('gallery_photos')->where('id', $photo->id)->update(['sort_order' => $index]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_photos', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
