<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\Cart;
use App\Models\Setting;
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
        // $PromptPayQR->id = getenv('PROMPTPAY_ID');
        $PromptPayQR->id = Setting::getValue('PROMPTPAY_ID', env('PROMPTPAY_ID'));
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

        require_once base_path("app/PromptPayQRCode/lib/PromptPayQR.php");

        $qrCodePath = public_path('qrcodes/payment_qr.png');

        $PromptPayQR = new PromptPayQR();
        $PromptPayQR->size = 5;
        $PromptPayQR->id = Setting::getValue('PROMPTPAY_ID', env('PROMPTPAY_ID'));
        $PromptPayQR->amount = $totalAmount;
        $PromptPayQR->generate($qrCodePath);

        $templatePath = public_path('../public/qrcodes/promptpay_template.jpg');
        $outputPath = public_path('qrcodes/final_payment_qr.png');

        $this->mergeQRCodeWithTemplate($templatePath, $qrCodePath, $outputPath, $totalAmount);

        return view('payment.bank', compact('orderItems', 'products', 'totalAmount'));
    }

    private function mergeQRCodeWithTemplate($templatePath, $qrCodePath, $outputPath, $totalAmount)
    {
        $template = imagecreatefromjpeg($templatePath); // โหลด template
        $qrCode = imagecreatefrompng($qrCodePath);      // โหลด QR Code

        $qrWidth = imagesx($qrCode);
        $qrHeight = imagesy($qrCode);

        $newQRWidth = 600;   // ขนาดใหม่ QR Code
        $newQRHeight = 600;
        $xPosition = 140;    // ตำแหน่ง QR Code (X) (เริ่มจากซ้ายไปขวา)
        $yPosition = 280;    // ตำแหน่ง QR Code (Y) (เริ่มจากบนลงล่าง)

        //  วาง QR Code ลงไปบน template
        imagecopyresampled(
            $template,     // ภาพปลายทาง (template)
            $qrCode,       // ภาพต้นทาง (QR Code)
            $xPosition,    // ตำแหน่ง X
            $yPosition,    // ตำแหน่ง Y
            0,
            0,          // เริ่มตัดจากต้นฉบับที่ 0,0
            $newQRWidth,   // ความกว้างใหม่
            $newQRHeight,  // ความสูงใหม่
            $qrWidth,      // ความกว้างเดิม
            $qrHeight      // ความสูงเดิม
        );

        //  เพิ่มข้อความยอดเงินใต้ QR Code
        $fontPath = public_path('../resources/fonts/Kanit/Kanit-Light.ttf');
        $fontItalicPath = public_path('../resources/fonts/Kanit/Kanit-LightItalic.ttf');
        $fontSize = 28;
        $textColor = imagecolorallocate($template, 0, 0, 0);
        $storeTextColor = imagecolorallocate($template, 140, 140, 140);

        $refNoText = 'Ref no. PD004990286422';

        $textBox = imagettfbbox(18, 0, $fontItalicPath, $refNoText);
        $textWidth = abs($textBox[4] - $textBox[0]);

        $textX = $xPosition + ($newQRWidth / 2) - ($textWidth / 2); // จัดข้อความให้ตรงกลาง QR
        $textY = $yPosition + $newQRHeight + 170; // ตำแหน่ง Y ใต้ QR Code

        imagettftext(
            $template,      // ภาพปลายทาง
            18,      // ขนาดฟอนต์
            0,              // ไม่มีการหมุน
            $textX,         // ตำแหน่ง X
            $textY,         // ตำแหน่ง Y
            $storeTextColor,     // สีข้อความ
            $fontItalicPath,      // ฟอนต์
            $refNoText // ข้อความ
        );

        $storeText = 'Perdis Store QrCode Payment';

        //  คำนวณความกว้างข้อความเพื่อให้ข้อความอยู่ตรงกลาง
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $storeText);
        $textWidth = abs($textBox[4] - $textBox[0]);

        $textX = $xPosition + ($newQRWidth / 2) - ($textWidth / 2); // จัดข้อความให้ตรงกลาง QR
        $textY = $yPosition + $newQRHeight + 125; // ตำแหน่ง Y ใต้ QR Code

        //  วาดข้อความบนภาพ โดยการหมุนข้อความเพื่อให้มันเป็นตัวเอียง
        imagettftext(
            $template,      // ภาพปลายทาง
            $fontSize,      // ขนาดฟอนต์
            0,              // ไม่มีการหมุน
            $textX,         // ตำแหน่ง X
            $textY,         // ตำแหน่ง Y
            $storeTextColor,     // สีข้อความ
            $fontPath,      // ฟอนต์
            $storeText // ข้อความ
        );

        $formattedAmount = "฿ " . number_format($totalAmount, 2);

        //  คำนวณความกว้างข้อความเพื่อให้ข้อความอยู่ตรงกลาง QR Code
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $formattedAmount);
        $textWidth = abs($textBox[4] - $textBox[0]);

        $textX = $xPosition + ($newQRWidth / 2) - ($textWidth / 2); // จัดข้อความให้ตรงกลาง QR
        $textY = $yPosition + $newQRHeight + 50; // ตำแหน่ง Y ใต้ QR Code

        //  วาดข้อความบนภาพ
        imagettftext(
            $template,      // ภาพปลายทาง
            $fontSize,      // ขนาดฟอนต์
            0,              // ไม่มีการหมุน
            $textX,         // ตำแหน่ง X
            $textY,         // ตำแหน่ง Y
            $textColor,     // สีข้อความ
            $fontPath,      // ฟอนต์
            $formattedAmount // ข้อความ
        );

        //  บันทึกภาพใหม่
        imagepng($template, $outputPath);

        //  ลบ resource เพื่อลด memory
        imagedestroy($template);
        imagedestroy($qrCode);
    }

    public function addLogoAndFrameToQRCode($qrCodePath)
    {
        $logoPath = public_path('../public/qrcodes/promptpay_logo.png');

        // ตรวจสอบว่าไฟล์โลโก้มีอยู่จริง
        if (!file_exists($logoPath)) {
            throw new \Exception("ไม่พบไฟล์โลโก้ที่: $logoPath");
        }

        // โหลด QR Code และโลโก้
        $qrImage = imagecreatefrompng($qrCodePath);
        $logoImage = imagecreatefrompng($logoPath);

        // ขนาดของ QR Code และโลโก้
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);
        $logoWidth = imagesx($logoImage);
        $logoHeight = imagesy($logoImage);

        // ขนาดกรอบและระยะห่าง
        $borderSize = 20;
        $logoMargin = 15;

        // สร้างภาพใหม่ (ใหญ่ขึ้น) สำหรับใส่โลโก้ด้านบน
        $newWidth = max($qrWidth, $logoWidth) + ($borderSize * 2);
        $newHeight = $qrHeight + $logoHeight + $logoMargin + ($borderSize * 2);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // สีพื้นหลัง (ขาว)
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // เพิ่มกรอบสีเทารอบ QR Code
        $gray = imagecolorallocate($newImage, 200, 200, 200);
        imagefilledrectangle(
            $newImage,
            $borderSize - 5,
            $logoHeight + $logoMargin - 5,
            $newWidth - $borderSize + 5,
            $logoHeight + $logoMargin + $qrHeight + 5,
            $gray
        );

        // แทรกโลโก้ไว้ด้านบนกลางภาพ
        $logoX = ($newWidth - $logoWidth) / 2;
        $logoY = $borderSize;
        imagecopy($newImage, $logoImage, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);

        // วาง QR Code ด้านล่างโลโก้
        $qrX = ($newWidth - $qrWidth) / 2;
        $qrY = $logoY + $logoHeight + $logoMargin;
        imagecopy($newImage, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);

        // บันทึกภาพใหม่
        imagepng($newImage, $qrCodePath);

        // ล้างหน่วยความจำ
        imagedestroy($qrImage);
        imagedestroy($logoImage);
        imagedestroy($newImage);
    }

    public function addFrameAndTextToQRCode($qrCodePath)
    {
        // โหลด QR Code
        $qrImage = imagecreatefrompng($qrCodePath);
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // ขนาดของกรอบและโลโก้
        $borderSize = 30;
        $logoHeight = 50;

        // สร้างภาพใหม่ที่ใหญ่ขึ้นเพื่อเพิ่มกรอบและข้อความ
        $newWidth = $qrWidth + ($borderSize * 2);
        $newHeight = $qrHeight + $borderSize + $logoHeight;
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // กำหนดสี
        $white = imagecolorallocate($newImage, 255, 255, 255); // พื้นหลังสีขาว
        $black = imagecolorallocate($newImage, 0, 0, 0);       // ข้อความสีดำ
        $gray = imagecolorallocate($newImage, 200, 200, 200);  // กรอบสีเทา

        // เติมพื้นหลังสีขาว
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $white);

        // วาดกรอบรอบ QR Code
        imagefilledrectangle(
            $newImage,
            $borderSize - 5,
            $logoHeight - 5,
            $newWidth - $borderSize + 5,
            $logoHeight + $qrHeight + 5,
            $gray
        );

        // วาง QR Code ลงในภาพใหม่
        imagecopy($newImage, $qrImage, $borderSize, $logoHeight, 0, 0, $qrWidth, $qrHeight);

        // เพิ่มข้อความ PromptPay ด้านบน
        $fontPath = public_path('../resources/fonts/Kanit/Kanit-Light.ttf'); // 📝 ใช้ฟอนต์ไทย
        $fontSize = 24;
        $text = 'PromptPay';
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $textBox[2] - $textBox[0];
        $textX = ($newWidth - $textWidth) / 2;
        $textY = $logoHeight - 15;
        imagettftext($newImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $text);

        // บันทึกภาพใหม่พร้อมกรอบและข้อความ
        imagepng($newImage, $qrCodePath);

        // ทำความสะอาดหน่วยความจำ
        imagedestroy($qrImage);
        imagedestroy($newImage);
    }

    private function addFrameAndTextToQRCode1($qrCodePath)
    {
        $qrImage = imagecreatefrompng($qrCodePath);
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        $borderSize = 40; // ขนาดขอบรอบ QR Code

        //  สร้างภาพใหม่ที่ใหญ่ขึ้นเพื่อเพิ่มขอบ
        $newWidth = $qrWidth + ($borderSize * 2);
        $newHeight = $qrHeight + ($borderSize * 2) + 50; // เผื่อพื้นที่สำหรับข้อความ
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        //  กำหนดสี
        $white = imagecolorallocate($newImage, 255, 255, 255); // สีพื้นหลัง
        $black = imagecolorallocate($newImage, 0, 0, 0);       // สีข้อความและกรอบ
        $blue = imagecolorallocate($newImage, 0, 102, 204);    // สีกรอบ (น้ำเงิน)

        //  เติมสีพื้นหลังเป็นสีขาว
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $white);

        //  วาดกรอบรอบ QR Code
        $frameThickness = 6;
        imagefilledrectangle($newImage, $borderSize - $frameThickness, $borderSize - $frameThickness, $qrWidth + $borderSize + $frameThickness - 1, $qrHeight + $borderSize + $frameThickness - 1, $blue);

        //  วาง QR Code ลงบนภาพใหม่
        imagecopy($newImage, $qrImage, $borderSize, $borderSize, 0, 0, $qrWidth, $qrHeight);

        //  เพิ่มข้อความ "PromptPay" ด้านล่าง
        $fontPath = public_path('../resources/fonts/Kanit/Kanit-Light.ttf'); // 📂 ใส่ไฟล์ฟอนต์ในโฟลเดอร์ public/fonts
        $fontSize = 16;
        $text = "PromptPay";
        $textColor = $black;

        // คำนวณตำแหน่งข้อความให้ตรงกลาง
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $textBox[2] - $textBox[0];
        $textX = ($newWidth - $textWidth) / 2;
        $textY = $newHeight - 20;

        imagettftext($newImage, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $text);

        //  บันทึกภาพใหม่ทับไฟล์เดิม
        imagepng($newImage, $qrCodePath);

        //  ลบภาพที่ใช้ในหน่วยความจำ
        imagedestroy($qrImage);
        imagedestroy($newImage);
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
