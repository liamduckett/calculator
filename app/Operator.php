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
     * @param int|Expression $first
     * @param int|Expression $second
     * @return int
     *
     * @throws InvalidExpressionException
     */
    function calculate(int|Expression $first, int|Expression $second): int
    {
        $first = match(gettype($first)) {
            'object' => $first->result(),
            'integer' => $first,
        };

        $second = match(gettype($second)) {
            'object' => $second->result(),
            'integer' => $second,
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
