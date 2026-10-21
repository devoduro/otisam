<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Rejects text containing HTML tags. Output is escaped anyway; this tells the writer early.
 * Maths such as "x < y and z > 3" still passes: a tag needs a letter, "/" or "!" right after "<".
 */
class PlainText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('~<[/!]?[a-z][^<>]*>~i', $value)) {
            $fail('Please remove HTML tags from the :attribute. Plain text only.');
        }
    }
}
