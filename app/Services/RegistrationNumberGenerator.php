<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;

class RegistrationNumberGenerator
{
    public function generate(Event $event): string
    {
        return DB::transaction(function () use ($event): string {
            $year = $event->event_date?->format('Y') ?? now()->format('Y');
            $prefix = "REG-{$year}-{$event->id}-";

            $latest = EventRegistration::query()
                ->where('event_id', $event->id)
                ->where('registration_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('registration_number')
                ->value('registration_number');

            $sequence = 1;
            if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
                $sequence = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        });
    }
}
