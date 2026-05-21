<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Example FormRequest demonstrating how uppercase enforcement
 * works with Laravel's validation layer.
 *
 * The UppercaseInput middleware runs BEFORE validation, so by the
 * time rules execute the text fields are already uppercase. This
 * request class only needs to define its normal validation rules.
 */
class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'department'  => ['required', 'string', 'max:100'],
            'designation' => ['required', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:500'],
            'city'        => ['nullable', 'string', 'max:100'],
            'state'       => ['nullable', 'string', 'max:100'],
            'country'     => ['nullable', 'string', 'max:100'],

            // Exception fields — NOT uppercased by middleware
            'email'       => ['required', 'email', 'max:255'],
            'username'    => ['nullable', 'string', 'max:50'],
            'password'    => ['required', 'string', 'min:8'],
        ];
    }
}
