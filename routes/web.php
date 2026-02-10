<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\WorkingPaperDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WorkingPaperDashboard::class, 'index'])->name('dashboard');

    // View media files
    Route::get('/view-expense-media/{expense}', function (\App\Models\ExpenseItem $expense) {
        if (!$expense->hasMedia('receipts')) {
            abort(404);
        }
        
        $media = $expense->getFirstMedia('receipts');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-expense');
    
    Route::get('/view-income-media/{income}', function (\App\Models\IncomeItem $income) {
        if (!$income->hasMedia('invoices')) {
            abort(404);
        }
        
        $media = $income->getFirstMedia('invoices');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-income');
    
    Route::get('/view-wage-media/{wageData}', function (\App\Models\WageData $wageData) {
        if (!$wageData->hasMedia('payg_summary')) {
            abort(404);
        }
        
        $media = $wageData->getFirstMedia('payg_summary');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-wage');

    Route::prefix('client')->name('client.')->group(function () {
        // Update selected work types
        Route::patch('/working-paper/{workingPaper}/types', [WorkingPaperDashboard::class, 'updateTypes'])
            ->name('working-paper.update-types');
        
        // Wage data
        Route::post('/working-paper/{workingPaper}/wage', [WorkingPaperDashboard::class, 'saveWageData'])
            ->name('wage.save');
        
        // Rental Property management
        Route::post('/working-paper/{workingPaper}/rental-property', [WorkingPaperDashboard::class, 'addRentalProperty'])
            ->name('rental-property.store');
        Route::delete('/rental-property/{rentalProperty}', [WorkingPaperDashboard::class, 'deleteRentalProperty'])
            ->name('rental-property.destroy');
        
        // Income items
        Route::post('/working-paper/{workingPaper}/income', [WorkingPaperDashboard::class, 'addIncome'])
            ->name('income.store');
        Route::delete('/income/{income}', [WorkingPaperDashboard::class, 'deleteIncome'])
            ->name('income.destroy');
        
        // Expense items
        Route::post('/working-paper/{workingPaper}/expense', [WorkingPaperDashboard::class, 'addExpense'])
            ->name('expense.store');
        Route::delete('/expense/{expense}', [WorkingPaperDashboard::class, 'deleteExpense'])
            ->name('expense.destroy');
        
        // Submit working paper
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
