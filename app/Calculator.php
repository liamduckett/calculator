<?php

namespace Liamduckett\Calculator;

class Calculator
{
    static function calculate(string $input): int
    {
        // split by operator
        $items = preg_split('/([+-])/', $input, flags: PREG_SPLIT_DELIM_CAPTURE);

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
            $first = $items[0];
            $operator = $items[1];
            $second = $items[2];

            // remove the first three items
            $items = array_slice($items, 3);

            // and swap them out for the result of the calculation
            array_unshift($items, $operator->calculate($first, $second));
        }

        return $items[0];
    }
}
