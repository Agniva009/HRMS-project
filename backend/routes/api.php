<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here pass through the UppercaseInput middleware (registered
| globally in Kernel.php), so textual input is uppercased automatically.
|
*/

Route::prefix('employees')->group(function () {
    Route::post('/', [EmployeeController::class, 'store']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
});
