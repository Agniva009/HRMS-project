<?php

namespace App\Helpers;

/**
 * Centralized helper for uppercase transformation.
 *
 * Use this in Eloquent mutators, FormRequest classes, or any place
 * where you need to enforce uppercase outside the middleware pipeline.
 *
 * Example in a Model:
 *   public function setNameAttribute(string $value): void
 *   {
 *       $this->attributes['name'] = UppercaseHelper::convert($value);
 *   }
 *
 * Example bulk conversion:
 *   $clean = UppercaseHelper::convertArray($data, ['email', 'password']);
 */
class UppercaseHelper
{
    /**
     * Convert a single string to uppercase (UTF-8 safe).
     */
    public static function convert(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert all string values in an associative array to uppercase,
     * skipping the listed exception keys.
     *
     * @param  array<string, mixed>  $data
     * @param  string[]              $except  keys to skip
     * @return array<string, mixed>
     */
    public static function convertArray(array $data, array $except = []): array
    {
        if (empty($except)) {
            $except = config('uppercase.except', []);
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $except, true)) {
                continue;
            }

            if (is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            } elseif (is_array($value)) {
                $data[$key] = static::convertArray($value, $except);
            }
        }

        return $data;
    }
}
