<?php

namespace App\Models;

use App\Services\RegistrationFieldOptionsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'event_participant_category_id',
        'registration_number',
        'category_label',
        'fee_amount',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'fee_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
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

    public function answers(): HasMany
    {
        return $this->hasMany(EventRegistrationAnswer::class);
    }

    public function answerMap(bool $forDisplay = true): array
    {
        $linkedOptions = app(RegistrationFieldOptionsService::class);

        return $this->answers->keyBy('field_key')->map(function (EventRegistrationAnswer $answer) use ($forDisplay, $linkedOptions) {
            if ($answer->field_type === RegistrationField::TYPE_FILE && $answer->file_path) {
                return basename($answer->file_path);
            }

            $value = $answer->value ?? '';

            if ($forDisplay) {
                return $linkedOptions->displayValue($answer->field_key, $value);
            }

            return $value;
        })->all();
    }
}
