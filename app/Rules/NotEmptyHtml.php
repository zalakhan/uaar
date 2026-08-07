<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotEmptyHtml implements ValidationRule
{
    /**
     * Reject HTML that contains no visible text content.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The content field is required.');

            return;
        }

        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (trim($text) === '') {
            $fail('The content field is required.');
        }
    }
}
