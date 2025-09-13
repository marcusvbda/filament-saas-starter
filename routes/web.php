<?php

use App\Filament\Company\Pages\PublicFillFormEvent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\EventsController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return redirect()->to("/company/login");
});

Route::group(["prefix" => "docusign"], function () {
    Route::post('webhook', [ContractsController::class, 'webhookDocusign'])->name('docusign.webhook')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::group(["middleware" => Authenticate::class], function () {
        Route::group(["prefix" => "contract"], function () {
            Route::get('{contract}', [ContractsController::class, 'generateContract'])->name('docusign.contract');
            Route::get('{contract}/print', [ContractsController::class, 'printContract'])->name('docusign.print_contract');
        });
        Route::get('callback', [ContractsController::class, 'callback'])->name('docusign.callback');
    });
});

Route::group(["prefix" => "event"], function () {
    Route::get('/fill-form/{key}', PublicFillFormEvent::class)->name('event.fill_data');
});
