<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand description storage for rich HTML content.
        DB::statement('ALTER TABLE news MODIFY description LONGTEXT NOT NULL');

        // Convert legacy plain-text descriptions to simple HTML paragraphs.
        DB::table('news')->orderBy('id')->lazyById()->each(function ($row) {
            $description = $row->description ?? '';

            if ($description === '' || $description !== strip_tags($description)) {
                return;
            }

            DB::table('news')->where('id', $row->id)->update([
                'description' => $this->wrapPlainTextAsHtml($description),
            ]);
        });

        // Map legacy string statuses to 1/0 before changing the column type.
        DB::table('news')->where('status', 'published')->update(['status' => '1']);
        DB::table('news')->where('status', 'draft')->update(['status' => '0']);

        DB::statement('ALTER TABLE news MODIFY status TINYINT(1) NOT NULL DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE news MODIFY status VARCHAR(255) NOT NULL DEFAULT 'draft'");

        DB::table('news')->where('status', '1')->update(['status' => 'published']);
        DB::table('news')->where('status', '0')->update(['status' => 'draft']);

        DB::statement('ALTER TABLE news MODIFY description TEXT NOT NULL');
    }

    /**
     * Wrap plain text in paragraph tags, preserving multiple paragraphs.
     */
    private function wrapPlainTextAsHtml(string $text): string
    {
        $paragraphs = preg_split('/\r\n|\r|\n/', trim($text)) ?: [];
        $html = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $html .= '<p>'.htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8').'</p>';
        }

        if ($html === '' && trim($text) !== '') {
            $html = '<p>'.htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8').'</p>';
        }

        return $html;
    }
};
