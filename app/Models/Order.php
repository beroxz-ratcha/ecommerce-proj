<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'total_price', 'seller_id', 'created_by', 'updated_by'];

    public function isPaid()
    {
        return $this->status === OrderStatus::Paid->value;
    }

    public function isDelivered()
    {
        return $this->status === OrderStatus::Delivered->value;
    }

    public function isWaitingForConfirmation()
    {
        return $this->status === OrderStatus::WaitingForConfirmation->value;
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function store()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function deleteUnpaidOrders($hours)
    {
        return Order::query()->where('status', OrderStatus::Unpaid->value)
            ->where('created_at', '<', Carbon::now()->subHours($hours))
            ->delete();
    }
}
