<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Unpaid = 'Unpaid';
    case Paid = 'Paid';
    case Delivered = 'Delivered';
    case Cancelled = 'Cancelled';
    case Shipped = 'Shipped';
    case Completed = 'Completed';
    case WaitingForConfirmation = 'Waiting For Confirmation';
    case PaymentPending = 'Payment Pending';

    public static function getStatuses(): array
    {
        return [
            self::Paid,
            self::Unpaid,
            self::Cancelled,
            self::Shipped,
            self::Completed,
            self::Delivered,
            self::WaitingForConfirmation,
            self::PaymentPending,
        ];
    }
}
