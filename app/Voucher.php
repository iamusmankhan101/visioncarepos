<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'is_active',
        'expires_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer'
    ];

    /**
     * Check if voucher is valid for use
     */
    public function isValid($order_amount = 0)
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check if expired
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        // Check minimum amount
        if ($this->min_amount && $order_amount < $this->min_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for given order total
     */
    public function calculateDiscount($order_amount)
    {
        if (!$this->isValid($order_amount)) {
            return 0;
        }

        $discount = 0;
        
        if ($this->discount_type === 'percentage') {
            $discount = ($order_amount * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        // Apply maximum discount limit if set
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }
}