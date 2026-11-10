<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventRegistrationExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRegistrationController extends Controller
{
    public function eventsIndex(Request $request): View
    {
        $this->authorize('viewAny', EventRegistration::class);

        $user = $request->user();

        $events = Event::query()
            ->with('department')
            ->withCount('registrations')
            ->when(! $user->isSuperAdmin(), function ($query) use ($user) {
                if ($user->isDepartmentAdmin() && $user->department_id) {
                    $query->where('department_id', $user->department_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('event_number', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.event-registrations.events-index', compact('events'));
    }

    public function index(Request $request, Event $event): View
    {
        Gate::authorize('event-registrations.view-event', $event);

        $event->load(['formFields' => fn ($q) => $q->orderBy('sort_order')]);

        $registrations = EventRegistration::query()
            ->where('event_id', $event->id)
            ->with('answers')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('registration_number', 'like', "%{$search}%")
                        ->orWhere('category_label', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn ($q) => $q->where('event_participant_category_id', $request->input('category')))
            ->orderByDesc('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $categories = $event->participantCategories()->orderBy('sort_order')->pluck('name', 'id');

        return view('admin.event-registrations.index', compact('event', 'registrations', 'categories'));
    }

    public function show(Event $event, EventRegistration $registration): View
    {
        $this->authorize('view', $registration);

        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $registration->load('answers');

        return view('admin.event-registrations.show', compact('event', 'registration'));
    }

    public function export(Event $event, EventRegistrationExporter $exporter): StreamedResponse
    {
        Gate::authorize('event-registrations.export', $event);

        return $exporter->download($event);
    }
}
