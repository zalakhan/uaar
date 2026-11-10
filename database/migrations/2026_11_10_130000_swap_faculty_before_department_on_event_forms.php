<?php

use App\Models\EventFormField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Faculty field should appear before Department on registration forms.
     */
    public function up(): void
    {
        DB::table('registration_fields')->where('field_key', 'faculty')->update(['sort_order' => 8]);
        DB::table('registration_fields')->where('field_key', 'department')->update(['sort_order' => 9]);

        $eventIds = EventFormField::query()
            ->whereIn('field_key', ['faculty', 'department'])
            ->distinct()
            ->pluck('event_id');

        foreach ($eventIds as $eventId) {
            $faculty = EventFormField::query()
                ->where('event_id', $eventId)
                ->where('field_key', 'faculty')
                ->first();
            $department = EventFormField::query()
                ->where('event_id', $eventId)
                ->where('field_key', 'department')
                ->first();

            if (! $faculty || ! $department) {
                continue;
            }

            if ($faculty->sort_order > $department->sort_order) {
                $facultyOrder = $faculty->sort_order;
                $faculty->update(['sort_order' => $department->sort_order]);
                $department->update(['sort_order' => $facultyOrder]);
            }
        }
    }

    public function down(): void
    {
        DB::table('registration_fields')->where('field_key', 'faculty')->update(['sort_order' => 9]);
        DB::table('registration_fields')->where('field_key', 'department')->update(['sort_order' => 8]);
    }
};
