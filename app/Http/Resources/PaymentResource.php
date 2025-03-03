<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'status' => $this->status,
            'amount' => $this->amount,
            'type' => $this->type,
            'session_id' => $this->session_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'payslip_img' => $this->payslip_img,
            'order' => new OrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
