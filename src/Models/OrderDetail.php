<?php

namespace Avanahuda\Avanatest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'qty',
        'price',
        'discount',
        'total',
        'order_id'
    ];
}
