<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Http\JsonResponse;

/**
 * Example controller showing that by the time the request reaches
 * the controller, all applicable fields are already UPPERCASE thanks
 * to the UppercaseInput middleware.
 */
class EmployeeController extends Controller
{
    /**
     * Store a new employee record.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // $validated['first_name'] is already "JOHN", etc.
        // $validated['email'] is still "user@example.com" (exception field).

        // Employee::create($validated);

        return response()->json([
            'message' => 'Employee created successfully.',
            'data'    => $validated,
        ], 201);
    }

    /**
     * Update an existing employee record.
     *
     * Edit-mode data also passes through the middleware, so any
     * modification is automatically uppercased.
     */
    public function update(StoreEmployeeRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        // Employee::findOrFail($id)->update($validated);

        return response()->json([
            'message' => 'Employee updated successfully.',
            'data'    => $validated,
        ]);
    }
}
