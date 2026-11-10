@php
    $readOnly = $readOnly ?? false;
    $prefix = $prefix ?? 'fields';
    $publicStyle = $publicStyle ?? false;
    $linkedFieldOptions = $linkedFieldOptions ?? app(\App\Services\RegistrationFieldOptionsService::class);
@endphp

@if ($publicStyle)
    <div class="public-form-grid public-form-grid--dynamic">
        @foreach ($resolvedFields as $item)
            @php
                $field = $item['field'];
                $required = $item['is_required'];
                $inputName = $prefix.'['.$field->field_key.']';
                $oldKey = $prefix.'.'.$field->field_key;
                $label = $field->label;
                $fieldId = 'field-'.$field->field_key;
            @endphp
            @if ($readOnly)
                <x-public.input :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey" disabled placeholder="{{ $field->placeholder }}" />
            @else
                @switch($field->field_type)
                    @case('textarea')
                        <x-public.textarea :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey"
                            :placeholder="$field->placeholder" :required="$required" />
                        @break
                    @case('select')
                        <x-public.select :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey" placeholder="Select..."
                            :options="$linkedFieldOptions->selectOptions($field)"
                            :required="$required" />
                        @break
                    @case('file')
                        <x-public.file :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey" :required="$required" />
                        @break
                    @case('date')
                        <x-public.input type="date" :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey"
                            :required="$required" />
                        @break
                    @case('email')
                        <x-public.input type="email" :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey"
                            :placeholder="$field->placeholder" :required="$required" />
                        @break
                    @case('tel')
                        <x-public.input type="tel" :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey"
                            :placeholder="$field->placeholder" :required="$required" />
                        @break
                    @default
                        <x-public.input type="text" :name="$inputName" :id="$fieldId" :label="$label" :validation-key="$oldKey"
                            :placeholder="$field->placeholder" :required="$required" />
                @endswitch
            @endif
        @endforeach
    </div>
@else
    @foreach ($resolvedFields as $item)
        @php
            $field = $item['field'];
            $required = $item['is_required'];
            $inputName = $prefix.'['.$field->field_key.']';
            $oldKey = $prefix.'.'.$field->field_key;
        @endphp
        <div class="mb-3 dynamic-field" data-field-key="{{ $field->field_key }}">
            <label class="form-label" for="field-{{ $field->field_key }}">
                {{ $field->label }}
                @if ($required)<span class="text-danger">*</span>@endif
            </label>

            @if ($readOnly)
                <input type="text" class="form-control" disabled placeholder="{{ $field->placeholder }}">
            @else
                @switch($field->field_type)
                    @case('textarea')
                        <textarea name="{{ $inputName }}" id="field-{{ $field->field_key }}" rows="3"
                                  class="form-control @error($oldKey) is-invalid @enderror"
                                  placeholder="{{ $field->placeholder }}"
                                  @if($required) required @endif>{{ old($oldKey) }}</textarea>
                        @break
                    @case('select')
                        <select name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                                class="form-select @error($oldKey) is-invalid @enderror"
                                @if($required) required @endif>
                            <option value="">Select...</option>
                            @foreach ($linkedFieldOptions->selectOptions($field) as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected((string) old($oldKey) === (string) $optionValue)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                        @break
                    @case('file')
                        <input type="file" name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                               class="form-control @error($oldKey) is-invalid @enderror"
                               @if($required) required @endif>
                        @break
                    @case('date')
                        <input type="date" name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                               value="{{ old($oldKey) }}"
                               class="form-control @error($oldKey) is-invalid @enderror"
                               @if($required) required @endif>
                        @break
                    @case('email')
                        <input type="email" name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                               value="{{ old($oldKey) }}"
                               class="form-control @error($oldKey) is-invalid @enderror"
                               placeholder="{{ $field->placeholder }}"
                               @if($required) required @endif>
                        @break
                    @case('tel')
                        <input type="tel" name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                               value="{{ old($oldKey) }}"
                               class="form-control @error($oldKey) is-invalid @enderror"
                               placeholder="{{ $field->placeholder }}"
                               @if($required) required @endif>
                        @break
                    @default
                        <input type="text" name="{{ $inputName }}" id="field-{{ $field->field_key }}"
                               value="{{ old($oldKey) }}"
                               class="form-control @error($oldKey) is-invalid @enderror"
                               placeholder="{{ $field->placeholder }}"
                               @if($required) required @endif>
                @endswitch
                @error($oldKey)<div class="invalid-feedback">{{ $message }}</div>@enderror
            @endif
        </div>
    @endforeach
@endif
