<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFeeRule extends Model
{
    protected $fillable = [
        'event_id',
        'event_participant_category_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participantCategory(): BelongsTo
    {
        return $this->belongsTo(EventParticipantCategory::class, 'event_participant_category_id');
    }
}
