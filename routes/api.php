<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\User\OrderController;
use App\Http\Controllers\Api\V1\User\IngredientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//real user application Apis
Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1'], function () {
    //Authentication Routes
    Route::post('auth/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
    Route::post('auth/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    //Authenticated user routes
        //Order Routes
    Route::post('order/create', [OrderController::class, 'createOrder']);
    Route::get('order/{id}', [OrderController::class, 'getOrderDetails']);
        //Ingredient Routes
    Route::get('ingredients', [IngredientController::class, 'getIngredients']);


    });

    Route::fallback(function () {
        return response()->json(['message' => 'Page Not Found'], 404);
    });
});

