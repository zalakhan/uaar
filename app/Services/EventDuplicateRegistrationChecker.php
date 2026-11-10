<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Str;

class EventDuplicateRegistrationChecker
{
    /** Fields used to detect the same person registering again for one event. */
    private const FINGERPRINT_KEYS = ['email', 'cnic', 'mobile'];

    public function findExisting(Event $event, array $fieldsInput): ?EventRegistration
    {
        $fingerprints = $this->fingerprintsFromInput($fieldsInput);
        if ($fingerprints === []) {
            return null;
        }

        $registrations = EventRegistration::query()
            ->where('event_id', $event->id)
            ->with(['answers' => function ($query): void {
                $query->whereIn('field_key', self::FINGERPRINT_KEYS);
            }])
            ->orderByDesc('id')
            ->get();

        foreach ($registrations as $registration) {
            foreach ($fingerprints as $key => $normalizedInput) {
                foreach ($registration->answers as $answer) {
                    if ($answer->field_key !== $key || $answer->value === null || $answer->value === '') {
                        continue;
                    }

                    if ($this->normalize($key, $answer->value) === $normalizedInput) {
                        return $registration;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function fingerprintsFromInput(array $fieldsInput): array
    {
        $fingerprints = [];

        foreach (self::FINGERPRINT_KEYS as $key) {
            $raw = $fieldsInput[$key] ?? null;
            if (! is_string($raw) || trim($raw) === '') {
                continue;
            }

            $fingerprints[$key] = $this->normalize($key, $raw);
        }

        return $fingerprints;
    }

    private function normalize(string $key, string $value): string
    {
        $value = trim($value);

        return match ($key) {
            'email' => Str::lower($value),
            'cnic', 'mobile' => preg_replace('/\D+/', '', $value) ?? '',
            default => $value,
        };
    }
}
