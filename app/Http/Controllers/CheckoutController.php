<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\Cart;
use App\Mail\NewOrderEmail;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $customer = $user->customer;
        if (!$customer->billingAddress || !$customer->shippingAddress) {
            return redirect()->route('profile')->with('error', 'Please provide your address details first.');
        }

        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $ordersData = [];
        $totalPrice = 0;

        DB::beginTransaction();

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            if ($product->quantity !== null && $product->quantity < $quantity) {
                $message = match ($product->quantity) {
                    0 => 'The product "' . $product->title . '" is out of stock',
                    1 => 'There is only one item left for product "' . $product->title . '"',
                    default => 'There are only ' . $product->quantity . ' items left for product "' . $product->title . '"',
                };
                return redirect()->back()->with('error', $message);
            }
        }

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $sellerId = $product->seller_id;

            if (!isset($ordersData[$sellerId])) {
                $ordersData[$sellerId] = [
                    'items' => [],
                    'total_price' => 0,
                    'line_items' => [],
                    'seller_id' => $sellerId,
                ];
            }

            $ordersData[$sellerId]['items'][] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ];
            $ordersData[$sellerId]['total_price'] += $product->price * $quantity;

            $ordersData[$sellerId]['line_items'][] = [
                'price_data' => [
                    'currency' => 'thb',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => $product->image ? [$product->image] : [],
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => $quantity,
            ];

            if ($product->quantity !== null) {
                $product->quantity -= $quantity;
                $product->save();
            }
        }

        try {
            foreach ($ordersData as $orderData) {
                $order = Order::create([
                    'total_price' => $orderData['total_price'],
                    'status' => OrderStatus::Unpaid,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'seller_id' => $orderData['seller_id'],
                ]);

                foreach ($orderData['items'] as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }
            }

            $allLineItems = [];
            foreach ($ordersData as $orderData) {
                $allLineItems = array_merge($allLineItems, $orderData['line_items']);
            }

            $session = \Stripe\Checkout\Session::create([
                'line_items' => $allLineItems,
                'mode' => 'payment',
                'customer_creation' => 'always',
                'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.failure', [], true),
            ]);

            foreach ($ordersData as $orderData) {
                $order = Order::where('seller_id', $orderData['seller_id'])->latest()->first();
                $paymentData = [
                    'order_id' => $order->id,
                    'amount' => $orderData['total_price'],
                    'status' => PaymentStatus::Pending,
                    'type' => 'cc',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'session_id' => $session->id
                ];
                Payment::create($paymentData);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }

        DB::commit();
        CartItem::where(['user_id' => $user->id])->delete();

        return redirect($session->url);
    }

    public function checkoutDelivery(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer->billingAddress || !$customer->shippingAddress) {
            return redirect()->route('profile')->with('error', 'Please provide your address details first.');
        }

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $totalPrice = 0;
        $ordersData = [];

        DB::beginTransaction();

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            if ($product->quantity !== null && $product->quantity < $quantity) {
                $message = match ($product->quantity) {
                    0 => 'The product "' . $product->title . '" is out of stock',
                    1 => 'There is only one item left for product "' . $product->title . '"',
                    default => 'There are only ' . $product->quantity . ' items left for product "' . $product->title . '"',
                };
                return redirect()->back()->with('error', $message);
            }
        }

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;

            $sellerId = $product->seller_id;
            if (!isset($ordersData[$sellerId])) {
                $ordersData[$sellerId] = [
                    'items' => [],
                    'total_price' => 0,
                    'seller_id' => $sellerId,
                ];
            }

            $ordersData[$sellerId]['items'][] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price
            ];
            $ordersData[$sellerId]['total_price'] += $product->price * $quantity;

            if ($product->quantity !== null) {
                $product->quantity -= $quantity;
                $product->save();
            }
        }

        try {
            $paymentTransId = 'PAY_' . substr(md5($user->id . microtime(true)), 0, 15);

            foreach ($ordersData as $orderData) {
                $order = Order::create([
                    'total_price' => $orderData['total_price'],
                    'status' => OrderStatus::Delivered,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'seller_id' => $orderData['seller_id'],
                ]);

                foreach ($orderData['items'] as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }

                $paymentData = [
                    'payment_trans_id' => $paymentTransId,
                    'order_id' => $order->id,
                    'amount' => $orderData['total_price'],
                    'status' => PaymentStatus::CashOnDelivery,
                    'type' => 'cod',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ];
                Payment::create($paymentData);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }

        DB::commit();
        CartItem::where(['user_id' => $user->id])->delete();
        return redirect()->route('checkout.success')->with('order_id', $order->id);
    }

    public function checkoutPayment(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer->billingAddress || !$customer->shippingAddress) {
            return redirect()->route('profile')->with('error', 'Please provide your address details first.');
        }

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $totalPrice = 0;
        $ordersData = [];

        DB::beginTransaction();

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            if ($product->quantity !== null && $product->quantity < $quantity) {
                $message = match ($product->quantity) {
                    0 => 'The product "' . $product->title . '" is out of stock',
                    1 => 'There is only one item left for product "' . $product->title . '"',
                    default => 'There are only ' . $product->quantity . ' items left for product "' . $product->title . '"',
                };
                return redirect()->back()->with('error', $message);
            }
        }

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;

            $sellerId = $product->seller_id;
            if (!isset($ordersData[$sellerId])) {
                $ordersData[$sellerId] = [
                    'items' => [],
                    'total_price' => 0,
                    'seller_id' => $sellerId,
                ];
            }

            $ordersData[$sellerId]['items'][] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price
            ];
            $ordersData[$sellerId]['total_price'] += $product->price * $quantity;

            if ($product->quantity !== null) {
                $product->quantity -= $quantity;
                $product->save();
            }
        }

        try {
            $paymentTransId = 'PAY_' . substr(md5($user->id . microtime(true)), 0, 15);

            foreach ($ordersData as $orderData) {
                $order = Order::create([
                    'total_price' => $orderData['total_price'],
                    'status' => OrderStatus::WaitingForConfirmation,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'seller_id' => $orderData['seller_id'],
                ]);

                foreach ($orderData['items'] as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }

                // อัปโหลดไฟล์สลิป
                if ($request->hasFile('slipInput')) {
                    // $slipImage = $request->file('slipInput');
                    // $slipImagePath = $slipImage->store('payslips', 'public'); // เก็บไฟล์ในโฟลเดอร์ payslips

                    $image = $request->file('slipInput');

                    $path = 'payslips'; // ไม่มีโฟลเดอร์สุ่มแล้ว
                    $name = Str::random(20) . '.' . $image->getClientOriginalExtension();

                    if (!Storage::exists('public/' . $path)) {
                        Storage::makeDirectory('public/' . $path, 0755, true);
                    }

                    if (!Storage::putFileAs('public/' . $path, $image, $name)) {
                        throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
                    }

                    $relativePath = $path . '/' . $name;

                    Payment::create([
                        'payment_trans_id' => $paymentTransId,
                        'order_id' => $order->id,
                        'amount' => $orderData['total_price'],
                        'status' => PaymentStatus::QRCode,
                        'type' => 'qrcode',
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                        'payslip_img' => $relativePath,
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }

        DB::commit();
        CartItem::where(['user_id' => $user->id])->delete();
        return redirect()->route('checkout.success')->with('order_id', $order->id);
    }

    public function success(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
        try {
            $session_id = $request->get('session_id');

            if ($session_id) {
                $session = \Stripe\Checkout\Session::retrieve($session_id);
                if (!$session) {
                    return view('checkout.failure', ['message' => 'Invalid Session ID']);
                }

                $payments = Payment::query()
                    ->where(['session_id' => $session_id])
                    ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                    ->get();

                if ($payments->isEmpty()) {
                    throw new NotFoundHttpException();
                }

                DB::beginTransaction();
                try {
                    foreach ($payments as $payment) {
                        if ($payment->status === PaymentStatus::Pending->value) {
                            $this->updateOrderAndSession($payment);
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
                    throw $e;
                }

                $customer = \Stripe\Customer::retrieve($session->customer);

                return view('checkout.success', compact('customer'));

            } else {
                $order_id = $request->session()->get('order_id');
                if (!$order_id) {
                    return view('checkout.failure', ['message' => 'Order ID is required']);
                }

                $payment = Payment::where('order_id', $order_id)->first();
                if (!$payment) {
                    throw new NotFoundHttpException();
                }
                $customerInfo = Customer::where('user_id', $payment->created_by)->first();
                if (!$customerInfo) {
                    return view('checkout.failure', ['message' => 'Customer not found']);
                }
                $customer = new \stdClass();
                $customer->name = $customerInfo->first_name . ' ' . $customerInfo->last_name;
                if ($payment->status === PaymentStatus::CashOnDelivery->value) {
                    return view('checkout.success', compact('customer'));
                } else if ($payment->status === PaymentStatus::QRCode->value) {
                    return view('checkout.success', compact('customer'));
                } else if ($payment->status === PaymentStatus::Bank->value) {
                    return view('checkout.success', compact('customer'));
                } else {
                    return view('checkout.failure', ['message' => 'Invalid payment status']);
                }

            }

        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure', ['message' => ""]);
    }

    public function checkoutOrder(Order $order, Request $request)
    {
        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

        if ($order->items->isEmpty()) {
            return redirect()->route('checkout.failure')->with('error', 'No item in Order.');
        }

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'thb',
                    'product_data' => [
                        'name' => $item->product->title,
                        // 'images' => $item->image ? [$item->image] : []
                    ],
                    'unit_amount' => (int) ($item->unit_price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        try {
            $session = \Stripe\Checkout\Session::create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.failure', [], true),
            ]);

            $order->payment->session_id = $session->id;
            $order->payment->save();

            Log::info('Stripe Checkout Session created: ' . $session->id);

            return redirect($session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::error('Stripe API error: ' . $e->getMessage());
            return redirect()->route('checkout.failure')->with('error', 'เกิดข้อผิดพลาดในการประมวลผลการชำระเงิน กรุณาลองอีกครั้ง.');
        } catch (\Exception $e) {

            Log::error('General error: ' . $e->getMessage());
            return redirect()->route('checkout.failure')->with('error', 'เกิดข้อผิดพลาด กรุณาลองอีกครั้ง.');
        }
    }


    public function webhook()
    {
        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

        $endpoint_secret = env('WEBHOOK_SECRET_KEY');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('', 401);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('', 402);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $paymentIntent = $event->data->object;
                $sessionId = $paymentIntent['id'];

                $payment = Payment::query()
                    ->where(['session_id' => $sessionId, 'status' => PaymentStatus::Pending])
                    ->first();
                if ($payment) {
                    $this->updateOrderAndSession($payment);
                }
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('', 200);
    }

    protected function updateQrCodePayment(Payment $payment)
    {
        $payment->status = PaymentStatus::WaitingForConfirmation->value;
        $payment->save();

        $order = $payment->order;

        $order->status = OrderStatus::WaitingForConfirmation->value;
        $order->save();

        $this->sendOrderNotificationEmails($order);
    }

    private function updateOrderAndSession(Payment $payment)
    {
        $payment->status = PaymentStatus::Paid->value;
        $payment->save();

        $order = $payment->order;

        $order->status = OrderStatus::Paid->value;
        $order->save();

        $this->sendOrderNotificationEmails($order);
    }

    private function sendOrderNotificationEmails(Order $order)
    {
        try {
            $adminUsers = User::where('role', 1)->get();

            foreach ($adminUsers as $user) {
                Mail::to($user)->send(new NewOrderEmail($order, true));
            }
            Mail::to($order->store)->send(new NewOrderEmail($order, false));
            Mail::to($order->user)->send(new NewOrderEmail($order, false));

        } catch (\Exception $e) {
            Log::critical('Email sending failed: ' . $e->getMessage());
        }
    }
}
