<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Globally converts all applicable string request fields to UPPERCASE.
 *
 * Fields listed in config('uppercase.except') are excluded (e.g. email,
 * password, URLs, tokens). The middleware handles both top-level and
 * nested (dot-notation / array) fields recursively.
 *
 * Register in app/Http/Kernel.php → $middleware (global) or
 * $middlewareGroups['api'].
 */
class UppercaseInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $except = config('uppercase.except', []);

        $input = $request->all();
        $request->merge($this->transform($input, $except));

        return $next($request);
    }

    /**
     * Recursively uppercase string values, skipping exception keys.
     *
     * @param  array<string, mixed>  $data
     * @param  string[]              $except
     * @param  string                $prefix  dot-notation prefix for nested keys
     * @return array<string, mixed>
     */
    private function transform(array $data, array $except, string $prefix = ''): array
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (in_array($key, $except, true) || in_array($fullKey, $except, true)) {
                continue;
            }

            if (is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            } elseif (is_array($value)) {
                $data[$key] = $this->transform($value, $except, $fullKey);
            }
        }

        return $data;
    }
}
