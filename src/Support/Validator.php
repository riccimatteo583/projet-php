<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\ValidationException;

/**
 * A tiny rule-based validator.
 *
 * Rules are expressed as pipe-delimited strings, for example:
 *   'title' => 'required|string|max:255'
 * On failure a ValidationException is thrown carrying all messages.
 */
final class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, mixed> The validated subset of the input.
     */
    public static function make(array $data, array $rules): array
    {
        $errors    = [];
        $validated = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $rulesList = explode('|', $ruleString);

            foreach ($rulesList as $rule) {
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);

                $message = self::check($name, $field, $value, $argument);

                if ($message !== null) {
                    $errors[$field][] = $message;
                }
            }

            if (array_key_exists($field, $data)) {
                $validated[$field] = $value;
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $validated;
    }

    /**
     * @param mixed $value
     */
    private static function check(string $name, string $field, mixed $value, ?string $argument): ?string
    {
        return match ($name) {
            'required' => ($value === null || $value === '')
                ? "The {$field} field is required."
                : null,
            'string' => ($value !== null && !is_string($value))
                ? "The {$field} field must be a string."
                : null,
            'boolean' => ($value !== null && !is_bool($value) && !in_array($value, [0, 1, '0', '1'], true))
                ? "The {$field} field must be a boolean."
                : null,
            'max' => (is_string($value) && $argument !== null && mb_strlen($value) > (int) $argument)
                ? "The {$field} field must not exceed {$argument} characters."
                : null,
            default => null,
        };
    }
}
