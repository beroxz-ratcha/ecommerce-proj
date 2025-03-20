<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGroupResource extends JsonResource
{
    public function toArray($request)
    {
        // กรองข้อมูลที่มี payment_trans_id เดียวกัน
        $group = $this->resource->where('payment_trans_id', $this->payment_trans_id);

        return [
            'payment_trans_id' => $this->payment_trans_id,
            'order_ids' => $group->pluck('order_id')->unique()->values()->all(),
            'total_amount' => $group->sum('amount'),
            'payments' => parent::toArray($request), // คืนค่าตาม PaymentResource
        ];
    }
}
