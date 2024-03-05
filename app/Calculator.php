<?php

namespace Liamduckett\Calculator;

class Calculator
{
    static function calculate(string $input): int
    {
        // split by operator
        $items = preg_split('/([+-])/', $input, flags: PREG_SPLIT_DELIM_CAPTURE);

        var_dump($items);


        foreach($items as $key => $item)
        {
            // even keys are operands
            // odd  keys are operations

            $items[$key] = match($key % 2 === 0) {
                true => (int) $item,
                false => Operator::from($item),
            };
        }

        $first = $items[0];
        $operator = $items[1];
        $second = $items[2];

        $items = array_slice($items, 3);

        array_unshift($items, $operator->calculate($first, $second));

        return array_sum($items);
    }
}
