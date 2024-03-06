<?php

namespace Liamduckett\Calculator;

use Exception;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Support\Collection;
use Liamduckett\Calculator\Support\Str;

class Calculator
{
    /**
     * @throws Exception
     */
    static function calculate(string $input): int
    {
        $tokens = static::tokenize($input);

        $expression = static::parse($tokens);

        return static::resolve($expression);
    }

    protected static function tokenize(string $input): Collection
    {
        return Str::make($input)
            // replace 'x' with '*'
            ->replace(search: 'x', replace: '*')
            // split by operator (accepts + - * and /)
            ->pregSplit('/([+\-*\/])/', flags: PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * @throws InvalidOperandException
     */
    protected static function parse(Collection $tokens): Collection
    {
        return $tokens->mapWithKeys(function(string $item, int $key) {
            return match($key % 2 === 0) {
                // even keys are operands
                true => is_numeric($item) ? (int) $item : throw new InvalidOperandException,
                // odd  keys are operators
                false => Operator::from($item),
            };
        });
    }

    protected static function resolve(Collection $expression): int
    {
        // until we have a single result
        while($expression->count() > 1) {
            // return the index of the most pressing operation
            $operatorIndex = $expression->searchMultiple([Operator::MULTIPLY, Operator::DIVIDE], default: 1);

            // extract the needed items for the most pressing operation
            [$first, $operator, $second] = $expression->slice($operatorIndex - 1, 3);

            // remove the above items and swap them out for the result of the calculation
            unset($expression[$operatorIndex - 1]);
            $expression[$operatorIndex] = $operator->calculate($first, $second);
            unset($expression[$operatorIndex + 1]);

            // sort by key
            $expression = $expression
                ->sortByKeys()
                ->values();
        }

        return $expression[0];
    }
}
