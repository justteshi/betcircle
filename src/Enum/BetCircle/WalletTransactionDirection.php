<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum WalletTransactionDirection: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}
