<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 * @package App
 * 
 * @property integer $order_id
 * @property integer $product_id
 */
class OrderItem extends Model
{
    public function order()
    {
        return $this->belongsTo(\App\Order::class);
    }

    public function product()
    {
        return $this->hasOne(\App\Product::class);
    }
}
