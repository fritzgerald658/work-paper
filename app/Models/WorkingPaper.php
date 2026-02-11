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
        'reviewed_by',
        'admin_comment',
        'reviewed_at',
    ];

    protected $casts = [
        'selected_types' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    /**
     * Check if working paper can be edited by client
     */
    public function canBeEditedByClient(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Check if working paper is waiting for admin review
     */
    public function isPendingReview(): bool
    {
        return in_array($this->status, ['submitted', 'resubmitted']);
    }

    /**
     * Check if working paper has been finalized
     */
    public function isFinalized(): bool
    {
        return in_array($this->status, ['approved']);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'resubmitted' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted - Pending Review',
            'resubmitted' => 'Resubmitted - Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected - Needs Revision',
            default => ucfirst($this->status),
        };
    }
}