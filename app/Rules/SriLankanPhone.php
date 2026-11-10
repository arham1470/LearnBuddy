<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SriLankanPhone implements ValidationRule
{
    /**
     * Regex matching valid Sri Lankan phone numbers:
     * Accepts:
     * - +94 7X XXXXXXX or +947XXXXXXXX (E.164)
     * - 07XXXXXXXX or 0XX XXXXXXX (Local standard with leading zero)
     * - 7XXXXXXXX (9 digits without trunk prefix)
     */
    public const PATTERN = '/^(?:\+94|0)?([1-9][0-9]{8})$/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        if (! is_string($value)) {
            $fail('The :attribute must be a valid phone number string.');

            return;
        }

        $cleaned = self::clean($value);

        if (! preg_match(self::PATTERN, $cleaned)) {
            $fail('Please enter a valid Sri Lankan phone number (e.g., +94 77 123 4567 or 0771234567).');
        }
    }

    /**
     * Remove spacing, dashes, and brackets from phone input.
     */
    public static function clean(string $phone): string
    {
        return preg_replace('/[\s\-\(\)\.]/', '', trim($phone));
    }

    /**
     * Normalize valid Sri Lankan phone to E.164 format (+94XXXXXXXXX).
     */
    public static function normalize(?string $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $cleaned = self::clean($phone);

        if (preg_match(self::PATTERN, $cleaned, $matches)) {
            return '+94'.$matches[1];
        }

        return $cleaned;
    }
}
