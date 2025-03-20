<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PaymentGroupResource;
use App\Models\Payment;
use App\Mail\OrderUpdateEmail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Enums\OrderStatus;

class PaymentController extends Controller
{
    public function index1()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Payment::with('order')
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return PaymentResource::collection($query);
    }

    public function index2()
    {

        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        if (!in_array($sortField, ['updated_at', 'created_at', 'amount', 'status'])) {
            $sortField = 'updated_at';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = Payment::with('order')
            ->whereNotNull('payment_trans_id')
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage); // ใช้ paginate() แทน get()

        return PaymentGroupResource::collection($query);
    }

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');
        $paymentTransId = request('payment_trans_id', null);

        $payments = Payment::with('order')
            ->whereNotNull('payment_trans_id')
            ->when($paymentTransId, function ($q) use ($paymentTransId) {
                $q->where('payment_trans_id', $paymentTransId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('payment_trans_id', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->get();

        $groupedPayments = $payments->groupBy('payment_trans_id');

        $result = $groupedPayments->map(function ($group, $paymentTransId) {
            $orderIds = $group->pluck('order_id')->unique()->values()->all();
            $totalAmount = $group->sum('amount');
            $status = optional($group->first())->status;
            $payslip_img = optional($group->first())->payslip_img;
            $payment_status = optional($group->first()->order)->status;

            return [
                'payment_trans_id' => $paymentTransId,
                'order_ids' => $orderIds,
                'status' => $status,
                'payslip_img' => $payslip_img,
                'payment_status' => $payment_status,
                'total_amount' => $totalAmount,
                'payments' => PaymentResource::collection($group)->toArray(request())
            ];
        })->values();

        $currentPage = request('page', 1);
        $paginated = $result->forPage($currentPage, $perPage);
        $total = $result->count();
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'data' => $paginated->values(),
            'links' => [
                'first' => $currentPage > 1 ? request()->url() . '?page=1' : null,
                'last' => $currentPage < $lastPage ? request()->url() . '?page=' . $lastPage : null,
                'prev' => $currentPage > 1 ? request()->url() . '?page=' . ($currentPage - 1) : null,
                'next' => $currentPage < $lastPage ? request()->url() . '?page=' . ($currentPage + 1) : null,
            ],
            'meta' => [
                'current_page' => $currentPage,
                'from' => $paginated->isEmpty() ? null : (($currentPage - 1) * $perPage + 1),
                'last_page' => $lastPage,
                'path' => request()->url(),
                'per_page' => $perPage,
                'to' => $paginated->isEmpty() ? null : (($currentPage - 1) * $perPage + $paginated->count()),
                'total' => $total
            ]
        ], 200);
    }

    public function show($payment_trans_id)
    {
        $query = Payment::with('order')
            ->where('payment_trans_id', $payment_trans_id)
            ->get();

        if ($query->isEmpty()) {
            return response()->json([
                'data' => [],
            ], 404);
        }

        $groupedPayments = collect($query)->groupBy('payment_trans_id');

        $result = $groupedPayments->map(function ($group, $paymentTransId) {
            $orderIds = $group->pluck('order_id')->unique()->values()->all();
            $totalAmount = $group->sum('amount');
            $status = $group->first()->status ?? null;
            $payslip_img = $group->first()->payslip_img ?? null;
            $payment_status = $group->first()?->order?->status ?? null;

            return [
                'payment_trans_id' => $paymentTransId,
                'order_ids' => $orderIds,
                'status' => $status,
                'payslip_img' => $payslip_img,
                'payment_status' => $payment_status,
                'total_amount' => $totalAmount,
                'payments' => PaymentResource::collection($group)->toArray(request())
            ];
        })->values();

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function changeStatus($payment_trans_id, $status)
    {
        DB::beginTransaction();
        try {
            $payments = Payment::where('payment_trans_id', $payment_trans_id)->get();

            if ($payments->isEmpty()) {
                throw new \Exception('Payments not found');
            }

            foreach ($payments as $payment) {
                $order = Order::find($payment->order_id);

                if (!$order) {
                    throw new \Exception('Order not found for payment ID: ' . $payment->id);
                }

                $payment->status = $status;
                $payment->save();

                $order->status = $status;
                $order->save();

                if ($status === OrderStatus::Cancelled->value) {
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        if ($product && $product->quantity !== null) {
                            $product->quantity += $item->quantity;
                            $product->save();
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return response('', 200);
    }
}