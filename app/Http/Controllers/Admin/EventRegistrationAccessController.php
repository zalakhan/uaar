<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistrationAccess;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventRegistrationAccessController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('grantAccess', $event);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super administrators already have full access.');
        }

        EventRegistrationAccess::firstOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ],
            [
                'granted_by' => $request->user()->id,
            ]
        );

        return back()->with('success', "Registration access granted to {$user->name}.");
    }

    public function destroy(Event $event, EventRegistrationAccess $access): RedirectResponse
    {
        $this->authorize('grantAccess', $event);

        if ($access->event_id !== $event->id) {
            abort(404);
        }

        $access->delete();

        return back()->with('success', 'Registration access removed.');
    }
}
