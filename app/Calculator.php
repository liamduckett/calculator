<?php

namespace Liamduckett\Calculator;

class Calculator
{
    static function calculate(string $input): int
    {
        // replace 'x' with '*'
        $input = str_replace('x', '*', $input);

        // split by operator
        $items = preg_split('/([+\-*\/])/', $input, flags: PREG_SPLIT_DELIM_CAPTURE);

        foreach($items as $key => $item)
        {
            // even keys are operands
            // odd  keys are operations

            $items[$key] = match($key % 2 === 0) {
                true => (int) $item,
                false => Operator::from($item),
            };
        }

        // until we have a single result
        while(count($items) > 1) {
            // multiplication needs to be
            $multiply = array_search(Operator::MULTIPLY, $items);

            $operatorIndex = match($multiply) {
                false => 1,
                default => $multiply,
            };

            $first = $items[$operatorIndex - 1];
            $operator = $items[$operatorIndex];
            $second = $items[$operatorIndex + 1];

            // remove the above items
            unset($items[$operatorIndex - 1]);
            unset($items[$operatorIndex]);
            unset($items[$operatorIndex + 1]);

            // and swap them out for the result of the calculation
            $items[$operatorIndex] = $operator->calculate($first, $second);

            // sort by key
            ksort($items);

            $items = array_values($items);
        }

        return $items[0];
    }
}
