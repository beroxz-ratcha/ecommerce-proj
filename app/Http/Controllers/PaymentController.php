<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\Cart;
use PromptPayQRCode\PromptPayQR;

class PaymentController extends Controller
{
    public function summary()
    {
        $cartItems = session()->get('cart', []);
        $totalPrice = array_sum(array_column($cartItems, 'total_price'));

        return view('payment.summary', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function generateQR(Request $request)
    {
        $amount = $request->input('amount');
        $qrCode = QrCode::size(200)->generate("PromptPay:$amount");

        return view('payment.qrcode', [
            'amount' => $amount,
            'qrCode' => $qrCode,
        ]);
    }

    public function generatePromptPayQR($total)
    {
        require_once base_path("app\PromptPayQRCode\lib\PromptPayQR.php");

        $PromptPayQR = new PromptPayQR();
        $PromptPayQR->size = 5;
        $PromptPayQR->id = '0944465110';
        $PromptPayQR->amount = $total;

        $qrCodePath = public_path('qrcodes/payment_qr.png');
        file_put_contents($qrCodePath, file_get_contents($PromptPayQR->generate()));

        return $qrCodePath;
    }

    public function showPaymentPage()
    {
        //test
        $totalAmount = 1000.00;

        $promptPayQR = new PromptPayQR();
        $promptPayQR->amount = $totalAmount;
        $qrCodeData = $promptPayQR->generate();

        return view('payment.page', [
            'totalAmount' => $totalAmount,
            'qrCodeData' => $qrCodeData,
        ]);
    }

    public function viewBank()
    {

        [$products, $orderItems] = Cart::getProductsAndCartItems();

        $totalAmount = 0;
        foreach ($products as $product) {
            $totalAmount += $product->price * $orderItems[$product->id]['quantity'];
        }

        require_once base_path("app\PromptPayQRCode\lib\PromptPayQR.php");

        $qrCodePath = public_path('qrcodes/payment_qr.png');

        $PromptPayQR = new PromptPayQR();
        $PromptPayQR->size = 5;
        $PromptPayQR->id = '0944465110';
        $PromptPayQR->amount = $totalAmount;
        $qrCodeData = $PromptPayQR->generate($qrCodePath);

        // ส่งข้อมูลทั้งหมดไปยัง View
        return view('payment.bank', compact('orderItems', 'products', 'totalAmount', 'qrCodeData'));
    }

    public function storeBankPayment(Request $request)
    {
        // ดำเนินการเกี่ยวกับข้อมูลการชำระเงินผ่านธนาคาร
        // เช่น การบันทึกการชำระเงินหรือส่งต่อไปยังระบบชำระเงินอื่นๆ
        return redirect()->route('payment.summary')->with('status', 'Payment successful');
    }

    public function selectPaymentMethod(Request $request)
    {
        $paymentMethod = $request->input('payment_method');
        return view('payment.select', [
            'paymentMethod' => $paymentMethod,
        ]);
    }
}
