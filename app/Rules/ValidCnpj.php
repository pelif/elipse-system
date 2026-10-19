<?php

namespace App\Rules;

use App\Domain\Customer\ValueObjects\Document;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('O campo :attribute deve ser um CNPJ válido.');

            return;
        }

        try {
            Document::fromString((string) $value);
        } catch (\InvalidArgumentException) {
            $fail('O campo :attribute deve ser um CNPJ válido.');
        }
    }
}
