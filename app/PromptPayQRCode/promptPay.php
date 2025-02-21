<?Php
use PromptPayQRCode\PromptPayQR;

require_once("lib/PromptPayQR.php");

$total = $_GET["total"];
$PromptPayQR = new PromptPayQR(); // new object
$PromptPayQR->size = 5; // Set QR code size to 8
$PromptPayQR->id = '0944465110'; // PromptPay ID
$PromptPayQR->amount = $total; // Set amount (not necessary)
echo '<img src="' . $PromptPayQR->generate() . '" />';