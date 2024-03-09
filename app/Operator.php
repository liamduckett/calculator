<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Exceptions\InvalidExpressionException;

enum Operator: string
{
    case ADD = '+';
    case SUBTRACT = '-';
    case MULTIPLY = '*';
    case DIVIDE = '/';
    case EXPONENTIATE = '^';

    /**
     * @param Expression $first
     * @param Expression $second
     * @return int
     *
     * @throws InvalidExpressionException
     */
    function calculate(Expression $first, Expression $second): int
    {
        $first = $first->result();

        $second = $second->result();

        return match($this) {
            self::ADD => $first + $second,
            self::SUBTRACT => $first - $second,
            self::MULTIPLY => $first * $second,
            self::DIVIDE => $first / $second,
            self::EXPONENTIATE => $first ** $second,
        };
    }
}
