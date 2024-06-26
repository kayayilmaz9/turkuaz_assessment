<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function campaign() {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }
}
