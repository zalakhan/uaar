<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistrationAnswer extends Model
{
    protected $fillable = [
        'event_registration_id',
        'event_form_field_id',
        'field_key',
        'label',
        'field_type',
        'value',
        'file_path',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(EventFormField::class, 'event_form_field_id');
    }
}
