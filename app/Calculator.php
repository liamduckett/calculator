<?php

namespace Liamduckett\Calculator;

use Exception;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Support\Str;

class Calculator
{
    /**
     * @throws Exception
     */
    static function calculate(string $input): int
    {
        $items = Str::make($input)
            // replace 'x' with '*'
            ->replace(search: 'x', replace: '*')
            // split by operator (accepts + - * and /)
            ->pregSplit('/([+\-*\/])/', flags: PREG_SPLIT_DELIM_CAPTURE);

        $items = $items->mapWithKeys(function(string $item, int $key) {
            return match($key % 2 === 0) {
                // even keys are operands
                true => is_numeric($item) ? (int) $item : throw new InvalidOperandException,
                // odd  keys are operators
                false => Operator::from($item),
            };
        });

        // until we have a single result
        while($items->count() > 1) {
            // multiplication needs to be prioritised
            $multiply = array_search(Operator::MULTIPLY, $items->toArray());
            $divide = array_search(Operator::DIVIDE, $items->toArray());

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
            $items = $items
                ->sortByKeys()
                ->values();
        }

        return $items[0];
    }
}
