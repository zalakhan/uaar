<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFieldCondition extends Model
{
    protected $fillable = [
        'event_form_field_id',
        'event_participant_category_id',
        'is_visible',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(EventFormField::class, 'event_form_field_id');
    }

    public function participantCategory(): BelongsTo
    {
        return $this->belongsTo(EventParticipantCategory::class, 'event_participant_category_id');
    }
}
