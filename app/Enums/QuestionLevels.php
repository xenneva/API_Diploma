<?php

namespace App\Enums;

enum QuestionLevels: int 
{
    case A_FIRST  = 1;
    case A_SECOND = 2;
    case B_FIRST  = 3;
    case B_SECOND = 4;
    case C_FIRST  = 5;
    case C_SECOND = 6;

    public static function toLine(int $level): string
    {
        return match($level) {
            self::A_FIRST->value  => 'A1',
            self::A_SECOND->value => 'A2',
            self::B_FIRST->value  => 'B1',
            self::B_SECOND->value => 'B2',
            self::C_FIRST->value  => 'C1',
            self::C_SECOND->value => 'C2'
        };
    }
}