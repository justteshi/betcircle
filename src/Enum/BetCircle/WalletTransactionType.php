<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum WalletTransactionType: string
{
    case TOKEN_PURCHASE = 'token_purchase';
    case WEEK_ENTRY = 'week_entry';

    case WEEKLY_PRIZE = 'weekly_prize';
    case SEASONAL_PRIZE = 'seasonal_prize';

    case MANUAL_CREDIT = 'manual_credit';
    case MANUAL_DEBIT = 'manual_debit';

    case REFUND = 'refund';
    case CORRECTION = 'correction';
}
