<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CurrencyController;
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

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('currency')->group(function () {
            Route::get('/', [CurrencyController::class, 'insertCurrency']);
            Route::get('update-rate', [CurrencyController::class, 'updateCurrencyRate']);
            Route::get('/{code}', [CurrencyController::class, 'getCurrencyRateByCode']);
        });

        Route::get('currencies', [CurrencyController::class, 'getAllCurrencies']);
    });

});
