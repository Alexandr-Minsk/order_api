<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package App
 * 
 * @property string $status
 */
class Order extends Model
{
    protected $fillable = ['status'];
    
    public function orderItems()
    {
        return $this->hasMany(\App\OrderItem::class);
    }
}
