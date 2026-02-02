<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\WorkingPaperDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WorkingPaperDashboard::class, 'index'])->name('dashboard');

    Route::prefix('client')->name('client.')->group(function () {
        Route::patch('/working-paper/{workingPaper}/types', [WorkingPaperDashboard::class, 'updateTypes'])
            ->name('working-paper.update-types');
        Route::post('/working-paper/{workingPaper}/wage', [WorkingPaperDashboard::class, 'saveWageData'])
            ->name('wage.save');
        Route::post('/working-paper/{workingPaper}/rental-property', [WorkingPaperDashboard::class, 'addRentalProperty'])
            ->name('rental-property.store');
        Route::post('/working-paper/{workingPaper}/income', [WorkingPaperDashboard::class, 'addIncome'])
            ->name('income.store');
        Route::delete('/income/{incomeItem}', [WorkingPaperDashboard::class, 'deleteIncome'])
            ->name('income.destroy');
        Route::post('/working-paper/{workingPaper}/expense', [WorkingPaperDashboard::class, 'addExpense'])
            ->name('expense.store');
        Route::delete('/expense/{expenseItem}', [WorkingPaperDashboard::class, 'deleteExpense'])
            ->name('expense.destroy');
        Route::post('/working-paper/{workingPaper}/submit', [WorkingPaperDashboard::class, 'submit'])
            ->name('working-paper.submit');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
