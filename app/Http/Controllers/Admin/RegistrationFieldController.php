<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationFieldController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', RegistrationField::class);

        $fields = RegistrationField::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(20);

        return view('admin.registration-fields.index', compact('fields'));
    }

    public function create(): View
    {
        $this->authorize('create', RegistrationField::class);

        return view('admin.registration-fields.create', ['fieldTypes' => $this->fieldTypes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', RegistrationField::class);

        $validated = $this->validateField($request);

        RegistrationField::create($validated);

        return redirect()
            ->route('admin.registration-fields.index')
            ->with('success', 'Registration field added to the library.');
    }

    public function edit(RegistrationField $registration_field): View
    {
        $this->authorize('update', $registration_field);

        return view('admin.registration-fields.edit', [
            'field' => $registration_field,
            'fieldTypes' => $this->fieldTypes(),
        ]);
    }

    public function update(Request $request, RegistrationField $registration_field): RedirectResponse
    {
        $this->authorize('update', $registration_field);

        $validated = $this->validateField($request, $registration_field);

        $registration_field->update($validated);

        return redirect()
            ->route('admin.registration-fields.index')
            ->with('success', 'Registration field updated.');
    }

    public function destroy(RegistrationField $registration_field): RedirectResponse
    {
        $this->authorize('delete', $registration_field);

        if ($registration_field->eventFormFields()->exists()) {
            return back()->with('error', 'This field is used on one or more events and cannot be deleted.');
        }

        $registration_field->delete();

        return redirect()
            ->route('admin.registration-fields.index')
            ->with('success', 'Registration field removed.');
    }

    /**
     * @return array<string, string>
     */
    private function fieldTypes(): array
    {
        return [
            RegistrationField::TYPE_TEXT => 'Text',
            RegistrationField::TYPE_TEXTAREA => 'Textarea',
            RegistrationField::TYPE_EMAIL => 'Email',
            RegistrationField::TYPE_TEL => 'Phone',
            RegistrationField::TYPE_DATE => 'Date',
            RegistrationField::TYPE_SELECT => 'Select',
            RegistrationField::TYPE_FILE => 'File upload',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateField(Request $request, ?RegistrationField $existing = null): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'field_key' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:registration_fields,field_key,'.($existing?->id ?? 'null')],
            'field_type' => ['required', 'string', 'in:text,textarea,email,tel,date,select,file'],
            'options_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $fieldKey = $validated['field_key'] ?? Str::slug($validated['label'], '_');

        $options = null;
        if ($validated['field_type'] === RegistrationField::TYPE_SELECT) {
            $options = collect(preg_split('/\r\n|\r|\n/', $validated['options_text'] ?? ''))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();

            if ($options === []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options_text' => 'Provide at least one option for select fields.',
                ]);
            }
        }

        return [
            'label' => $validated['label'],
            'field_key' => $fieldKey,
            'field_type' => $validated['field_type'],
            'options' => $options,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}
