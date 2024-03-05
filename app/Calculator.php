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
            // return the index of the most pressing operation
            $operatorIndex = $items->searchMultiple([Operator::MULTIPLY, Operator::DIVIDE], default: 1);

            // extract the needed items for the most pressing operation
            [$first, $operator, $second] = $items->slice($operatorIndex - 1, 3);

            // remove the above items and swap them out for the result of the calculation
            unset($items[$operatorIndex - 1]);
            $items[$operatorIndex] = $operator->calculate($first, $second);
            unset($items[$operatorIndex + 1]);

            // sort by key
            $items = $items
                ->sortByKeys()
                ->values();
        }

        return $items[0];
    }
}
