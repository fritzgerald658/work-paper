<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkingPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_year',
        'selected_types',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'selected_types' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wageData(): HasOne
    {
        return $this->hasOne(WageData::class);
    }

    public function rentalProperties(): HasMany
    {
        return $this->hasMany(RentalProperty::class);
    }

    public function incomeItems(): HasMany
    {
        return $this->hasMany(IncomeItem::class);
    }

    public function expenseItems(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }

    /**
     * Check if a specific work type is selected
     */
    public function hasType(string $type): bool
    {
        return in_array($type, $this->selected_types ?? []);
    }

    /**
     * Get available work types
     */
    public static function getAvailableTypes(): array
    {
        return [
            'wage' => 'Wage',
            'rental' => 'Rental Property',
            'sole_trader' => 'Sole Trader',
            'bas' => 'BAS',
            'ctax' => 'Company Tax',
            'ttax' => 'Trust Tax',
            'smsf' => 'SMSF',
        ];
    }
}