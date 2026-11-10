<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventFormField extends Model
{
    protected $fillable = [
        'event_id',
        'registration_field_id',
        'field_key',
        'label',
        'placeholder',
        'field_type',
        'options',
        'validation_rules',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation_rules' => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrationField(): BelongsTo
    {
        return $this->belongsTo(RegistrationField::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(EventFieldCondition::class);
    }
}
