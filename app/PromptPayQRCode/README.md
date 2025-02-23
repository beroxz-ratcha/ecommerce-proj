# PromptPay-QR-generator

ไลบารี่ใช้สร้าง PromptPay QR สำหรับจ่ายเงินผ่าน QR code รองรับการตั้ง PromptPay ID ทั้งหมายเลขบัตรประจำตัวประชาชน และเบอร์โทรศัพท์

ตัวอย่าง

```php
<?Php
require_once("lib/PromptPayQR.php");

$PromptPayQR = new PromptPayQR(); // new object
$PromptPayQR->size = 8; // Set QR code size to 8
$PromptPayQR->id = 'xxxxxxxx'; // PromptPay ID
$PromptPayQR->amount = 200.25; // Set amount (not necessary)
echo '<img src="' . $PromptPayQR->generate() . '" />';
```

## ไลบารี่ที่ใช้ร่วม

- PHP QrCode Liblary : http://phpqrcode.sourceforge.net/
