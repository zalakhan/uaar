<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'event_number',
        'title',
        'description',
        'event_type',
        'department_id',
        'event_date',
        'registration_deadline',
        'venue',
        'status',
        'public_slug',
        'fees_enabled',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'registration_deadline' => 'datetime',
            'fees_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            if (empty($event->public_slug)) {
                $event->public_slug = Str::lower(Str::random(32));
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participantCategories(): HasMany
    {
        return $this->hasMany(EventParticipantCategory::class)->orderBy('sort_order');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(EventFormField::class)->orderBy('sort_order');
    }

    public function feeRules(): HasMany
    {
        return $this->hasMany(EventFeeRule::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registrationAccess(): HasMany
    {
        return $this->hasMany(EventRegistrationAccess::class);
    }

    public function isOpenForRegistration(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        return true;
    }

    public function publicRegistrationUrl(): string
    {
        return route('event-registration.show', $this->public_slug);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_CLOSED => 'Closed',
            default => 'Draft',
        };
    }
}
