<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class OrderStatusEnum extends Enum
{
    const NEW = 1;
    const ACCEPTED = 2;
    const REJECTED = 3;
    const PROCESSING = 4;
    const HIGH_PRIORITY_PROCESSING = 5;
    const NO_RESPONSE = 6;
    const INQUIRY = 7;
    const CANCELED = 8;
    const COMPLETED = 9;
    const DOCUMENTS_DELIVERED = 10;
}
