<?php

namespace Liamduckett\Calculator;

enum Operator: string
{
    case ADD = '+';
    case SUBTRACT = '-';
    case MULTIPLY = '*';
    case DIVIDE = '/';
    case EXPONENTIATE = '^';

    /**
     * @param int|Expression $first
     * @param int|Expression $second
     * @return int
     */
    function calculate(int|Expression $first, int|Expression $second): int
    {
        $first = match($first instanceof Expression) {
            true => $first->result(),
            false => $first,
        };

        $second = match($second instanceof Expression) {
            true => $second->result(),
            false => $second,
        };

        return match($this) {
            self::ADD => $first + $second,
            self::SUBTRACT => $first - $second,
            self::MULTIPLY => $first * $second,
            self::DIVIDE => $first / $second,
            self::EXPONENTIATE => $first ** $second,
        };
    }
}
