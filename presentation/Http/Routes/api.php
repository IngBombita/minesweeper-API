<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->middleware('throttle:60')->group(function () {
    Route::post('/login', [CreateTokenAction::class, '__invoke'])->name('token.create');
});
