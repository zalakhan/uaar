<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventNumberGenerator
{
    public function generate(?int $year = null): string
    {
        $year = $year ?? (int) now()->format('Y');

        return DB::transaction(function () use ($year): string {
            $prefix = "EVT-{$year}-";

            $latest = Event::query()
                ->where('event_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('event_number')
                ->value('event_number');

            $sequence = 1;
            if ($latest && preg_match('/EVT-\d{4}-(\d+)$/', $latest, $matches)) {
                $sequence = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
        });
    }
}
