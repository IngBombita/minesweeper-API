<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->middleware('throttle:60')->group(function () {
    Route::get('/', [\Presentation\Http\Actions\WelcomeAction::class, '__invoke'])->name('welcome');

    Route::prefix('/games')->group(function () {
        Route::post('/', [CreateGameAction::class, '__invoke'])->name('game.create');
        Route::get('/', [ListGamesAction::class, '__invoke'])->name('game.list');
        Route::get('/{id}', [GetGameStatsAction::class, '__invoke'])->name('game.stats');

        Route::patch('/{id}/cell', [UpdateCellAction::class, '__invoke'])->name('cell.update');
    });

});
