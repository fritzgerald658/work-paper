<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ExpenseItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'working_paper_id',
        'rental_property_id',
        'section_type',
        'field_type',
        'description',
        'amount_inc_gst',
        'gst_amount',
        'net_ex_gst',
        'quarter',
        'client_comment',
        'own_comment',
    ];

    protected $casts = [
        'amount_inc_gst' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'net_ex_gst' => 'decimal:2',
    ];

    public function workingPaper(): BelongsTo
    {
        return $this->belongsTo(WorkingPaper::class);
    }

    public function rentalProperty(): BelongsTo
    {
        return $this->belongsTo(RentalProperty::class);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipts')
            ->useDisk('public');
    }

    /**
     * Calculate GST from amount inc GST (10% rate)
     */
    public function calculateGst(): void
    {
        if ($this->amount_inc_gst) {
            $this->net_ex_gst = round($this->amount_inc_gst / 1.1, 2);
            $this->gst_amount = round($this->amount_inc_gst - $this->net_ex_gst, 2);
        }
    }

    /**
     * Validate GST calculation
     */
    public function validateGst(): bool
    {
        if (!$this->amount_inc_gst || !$this->gst_amount || !$this->net_ex_gst) {
            return false;
        }

        $calculatedTotal = $this->net_ex_gst + $this->gst_amount;
        $difference = abs($calculatedTotal - $this->amount_inc_gst);

        // Allow 0.01 tolerance for rounding
        return $difference <= 0.01;
    }
}