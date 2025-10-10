<?php

use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('vending')->group(function () {
    Route::get('/', [VendingMachineController::class, 'index']);
    Route::post('/insert-coin', [VendingMachineController::class, 'insertCoin']);
    Route::post('/return-coin', [VendingMachineController::class, 'returnCoins']);
    Route::post('/buy-item', [VendingMachineController::class, 'buyItem']);

    Route::post('/update-coins-machine', [VendingMachineController::class, 'updateCoinsMachine']);
});
