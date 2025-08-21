<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractsController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::group(["prefix" => "docusign"], function () {
    Route::post('webhook', [ContractsController::class, 'webhookDocusign'])->name('docusign.webhook')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::group(["middleware" => Authenticate::class], function () {
        Route::get('contract/{contract}', [ContractsController::class, 'generateContract'])->name('docusign.contract');
        Route::get('contract/{contract}/print', [ContractsController::class, 'printContract'])->name('docusign.print_contract');
        Route::get('callback', [ContractsController::class, 'callback'])->name('docusign.callback');
    });
});

Route::group(["prefix" => "santander"], function () {
    Route::post('webhook', [ContractsController::class, 'webhookSantander'])->name('docusign.webhook')->withoutMiddleware([VerifyCsrfToken::class]);
});
