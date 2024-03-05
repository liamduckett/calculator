<?php

namespace Liamduckett\Calculator;

enum Operator: string
{
    case ADD = '+';
    case SUBTRACT = '-';
    case MULTIPLY = '*';
    case DIVIDE = '/';

    function calculate(int $first, int $second): int
    {
        return match($this) {
            self::ADD => $first + $second,
            self::SUBTRACT => $first - $second,
            self::MULTIPLY => $first * $second,
            self::DIVIDE => $first / $second,
        };
    }
}
