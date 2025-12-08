<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HostingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'features',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'image',
    ];

    protected $casts = [
        'features' => 'array',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    /**
     * Check if the plan currently has an active discount.
     */
    public function hasActiveDiscount()
    {
        if (is_null($this->discount_price)) {
            return false;
        }

        $now = Carbon::now();

        if ($this->discount_start_date && $now->lt($this->discount_start_date)) {
            return false;
        }

        if ($this->discount_end_date && $now->gt($this->discount_end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get the effective price (discounted if active, otherwise regular).
     */
    public function getEffectivePriceAttribute()
    {
        return $this->hasActiveDiscount() ? $this->discount_price : $this->price;
    }
}
