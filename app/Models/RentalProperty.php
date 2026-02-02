<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'working_paper_id',
        'address_label',
        'ownership_percentage',
        'period_rented',
    ];

    protected $casts = [
        'ownership_percentage' => 'decimal:2',
    ];

    public function workingPaper(): BelongsTo
    {
        return $this->belongsTo(WorkingPaper::class);
    }

    public function incomeItems(): HasMany
    {
        return $this->hasMany(IncomeItem::class);
    }

    public function expenseItems(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }
}