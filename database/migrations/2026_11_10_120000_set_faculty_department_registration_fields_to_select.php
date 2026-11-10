<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Faculty and department library fields use live DB dropdowns.
     */
    public function up(): void
    {
        foreach (['faculty', 'department'] as $fieldKey) {
            DB::table('registration_fields')
                ->where('field_key', $fieldKey)
                ->update([
                    'field_type' => 'select',
                    'options' => null,
                    'updated_at' => now(),
                ]);

            DB::table('event_form_fields')
                ->where('field_key', $fieldKey)
                ->update([
                    'field_type' => 'select',
                    'options' => null,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        foreach (['faculty', 'department'] as $fieldKey) {
            DB::table('registration_fields')
                ->where('field_key', $fieldKey)
                ->update(['field_type' => 'text', 'options' => null]);

            DB::table('event_form_fields')
                ->where('field_key', $fieldKey)
                ->update(['field_type' => 'text', 'options' => null]);
        }
    }
};
