<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Event;
use App\Services\EventNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * List events (super admin sees all).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->with('department')
            ->withCount('registrations')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('event_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('department'), fn ($q) => $q->where('department_id', $request->input('department')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('admin.events.index', compact('events', 'departments'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('admin.events.create', $this->formData());
    }

    public function store(Request $request, EventNumberGenerator $numberGenerator): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'event_date' => ['nullable', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
        ]);

        $event = Event::create([
            ...$validated,
            'event_number' => $numberGenerator->generate(),
            'status' => Event::STATUS_DRAFT,
            'created_by' => $request->user()->id,
            'fees_enabled' => false,
        ]);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event created. Configure the registration form before publishing.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load([
            'department',
            'participantCategories',
            'formFields',
            'feeRules.participantCategory',
        ]);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('admin.events.edit', array_merge(
            ['event' => $event],
            $this->formData()
        ));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'event_date' => ['nullable', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
        ]);

        $event->update($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        if ($event->participantCategories()->count() === 0) {
            return back()->with('error', 'Add at least one participant category before publishing.');
        }

        if ($event->formFields()->count() === 0) {
            return back()->with('error', 'Configure registration form fields before publishing.');
        }

        $event->update(['status' => Event::STATUS_PUBLISHED]);

        return back()->with('success', 'Event published. Share the public registration link.');
    }

    public function close(Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $event->update(['status' => Event::STATUS_CLOSED]);

        return back()->with('success', 'Event registration closed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
