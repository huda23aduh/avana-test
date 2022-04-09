<?php

namespace Avanahuda\Avanatest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'amount_paid',
        'payment_date',
        'status',
        'order_id'
    ];
}
