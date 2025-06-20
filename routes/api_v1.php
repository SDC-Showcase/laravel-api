<?php

use App\Http\Controllers\Api\V1\FieldValuesController;
use App\Http\Controllers\Api\V1\PlantController;
use App\Http\Controllers\Api\V1\ReferenceController;
use App\Http\Controllers\EhalophReferenceController;
use App\Http\Resources\V1\FieldResource;
use App\Models\Api\V1\ApiField;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/


Route::middleware('throttle:120,1')->group(function () {

    Route::get('/plants/{id}', [PlantController::class, 'show']);
    Route::apiResource('/plants', PlantController::class)->except(['show']);

    Route::get('/fields', function () {
        return FieldResource::collection(ApiField::orderBy('position')->get());
    });

    Route::get('/fieldvalues/{fieldName}', [FieldValuesController::class, 'getFieldValues']);


    Route::get('/referencelabels', function () {
        $labels = EhalophReferenceController::get_ref_labels();
        $labels = ['data' => $labels];
        return $labels;
    });

    Route::get('references/{reference}', [ReferenceController::class, 'show']);
    Route::apiResource('references', ReferenceController::class)->except(['show']);


});
