<?php

namespace Liamduckett\Calculator;

use Exception;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;

class Calculator
{
    /**
     * @throws Exception
     */
    static function calculate(string $input): int
    {
        // replace 'x' with '*'
        $input = str_replace('x', '*', $input);

        // split by operator (accepts + - * and /)
        $items = preg_split('/([+\-*\/])/', $input, flags: PREG_SPLIT_DELIM_CAPTURE);

        foreach($items as $key => $item)
        {
            // even keys are operands
            // odd  keys are operations

            $items[$key] = match($key % 2 === 0) {
                true => is_numeric($item) ? (int) $item : throw new InvalidOperandException,
                false => Operator::from($item),
            };
        }

        // until we have a single result
        while(count($items) > 1) {
            // multiplication needs to be prioritised
            $multiply = array_search(Operator::MULTIPLY, $items);
            $divide = array_search(Operator::DIVIDE, $items);

            // if it has '*' then return index of it
            if($multiply !== false) {
                $operatorIndex = $multiply;
            }
            // elif it has '/' then return index of it
            elseif($divide !== false) {
                $operatorIndex = $divide;
            }
            // else return 1
            else {
                $operatorIndex = 1;
            }

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
