<?php

namespace App\Models\Commerce;

use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['coupon_id', 'user_id', 'payment_id'];
}
