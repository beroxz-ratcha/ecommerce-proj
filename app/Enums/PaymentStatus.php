<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Failed = 'Failed';
    case CashOnDelivery = 'Cash On Delivery';
    case Bank = 'Bank';
    case QRCode = 'QRCode';
    case WaitingForConfirmation = 'Waiting For Confirmation';
    case Refunded = 'Refunded';
    case Cancelled = 'Cancelled';
}
