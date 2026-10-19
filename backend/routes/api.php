<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\CompositionController;
use App\Http\Controllers\Api\IllustrationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/ping', fn () => response()->json(['status' => 'ok', 'message' => 'Vector Constructor API is online']));

    // Illustrations routes
    Route::apiResource('illustrations', IllustrationController::class)->only(['index', 'show', 'store']);

    // Components routes
    Route::apiResource('components', ComponentController::class);

    // Compositions routes
    Route::apiResource('compositions', CompositionController::class);
});

