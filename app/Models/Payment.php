<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['payment_trans_id', 'order_id', 'status', 'amount', 'type', 'session_id', 'created_by', 'updated_by', 'payslip_img'];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
