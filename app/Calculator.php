<?php

namespace Liamduckett\Calculator;

use Exception;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Exceptions\InvalidOperatorException;
use Liamduckett\Calculator\Exceptions\MissingLastOperandException;
use Liamduckett\Calculator\Support\Collection;

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
        // allow use of 'x' as '*'
        $input = str_replace('x', '*', $input);
        // remove spaces
        $input = str_replace(' ', '', $input);

        $items = [];
        $item = '';
        $index = 0;

        // Loop through the string
        // Look for operand, until a non-numeric character is found
        // Next character HAS to be an operation

        while($index < strlen($input)) {
            while(is_numeric($input[$index])) {
                $item .= $input[$index];
                $index += 1;
            }

            // char is no longer numeric
            // previous chars are first operand
            $items[] = $item;
            $item = '';

            // there may not be an operator, this could be the end of the string...
            if($index === strlen($input)) {
                break;
            }

            // operator must be the current character
            $items[] = $input[$index];
            $index += 1;
        }

        return new Collection($items);
    }

    /**
     * @throws InvalidOperandException
     */
    protected static function parse(Collection $tokens): Collection
    {
        $tokens = $tokens->mapWithKeys(function(string $item, int $key) {
            return match($key % 2 === 0) {
                // even keys are operands
                true => is_numeric($item) ? (int) $item : throw new InvalidOperandException,
                // odd  keys are operators
                false => Operator::from($item),
            };
        });

        if($tokens[array_key_last($tokens->toArray())] instanceof Operator) {
            throw new InvalidOperandException;
        }

        return $tokens;
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
