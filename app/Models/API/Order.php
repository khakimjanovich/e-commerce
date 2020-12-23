<?php

namespace App\Models\API;

use App\Cart\Money;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'status',
        'address_id',
        'subtotal',
        'shipping_method_id',
        'payment_method_id'
    ];
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const PAYMENT_FAILED = 'payment_failed';
    const COMPLETED = 'completed';

    public static function boot()
    {
        parent::boot();
        static::creating(function($order){
           $order->status = self::PENDING;
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute($subtotal)
    {
        return new Money($subtotal);
    }

    public function total()
    {
        return $this->subtotal->add($this->shippingMethod->price);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function products()
    {
        return $this->belongsToMany(ProductVariation::class,'product_variation_order')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
