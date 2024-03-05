<?php

namespace Liamduckett\Calculator;

class Calculator
{
    static function calculate(string $input): int
    {
        // split by operator
        $operands = explode('+', $input);

        // turn the operands into integers
        $operands = array_map(
            fn(string $operand) => (int) $operand,
            $operands,
        );

        return array_sum($operands);
    }
}
