<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum PredictionOutcome: string
{
    case HOME_WIN = 'home_win';
    case DRAW = 'draw';
    case AWAY_WIN = 'away_win';
}
