<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->can('event_registrations.view')) {
            return true;
        }

        return $user->canAccessEventRegistrationsModule();
    }

    public function view(User $user, EventRegistration $registration): bool
    {
        return $this->canAccessEvent($user, $registration->event);
    }

    public function viewEvent(User $user, Event $event): bool
    {
        return $this->canAccessEvent($user, $event);
    }

    private function canAccessEvent(User $user, Event $event): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canViewRegistrationsForEvent($event);
    }

    public function export(User $user, Event $event): bool
    {
        return $this->canAccessEvent($user, $event);
    }
}
