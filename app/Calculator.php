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

    protected static function tokenize(string $input): array
    {
        $input = Str::make($input)
            // allow use of 'x' as '*'
            ->replace(search: 'x', replace: '*')
            // remove spaces
            ->replace(search: ' ', replace: '');

        $items = [];
        $item = '';
        $index = 0;

        // Loop through the string
        // Look for operand, until a non-numeric character is found
        // Next character HAS to be an operation

        while($index < $input->length()) {
            // if we have an open bracket
            if($input->charAt($index)->toString() === '(') {
                $index += 1;

                // loop through until we find a closed bracket
                while($input->charAt($index)->toString() !== ')') {
                    $item .= $input->charAt($index);
                    $index += 1;
                }

                // skip the closed bracket
                $index += 1;
                // recursive call
                $items[] = static::tokenize($item);
                $item = '';
            }

            // there may not be an operand here, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            while($input->charAt($index)->isNumeric()) {
                $item .= $input->charAt($index);
                $index += 1;
            }

            // char is no longer numeric
            // previous chars are first operand
            $items[] = $item;
            $item = '';

            // there may not be an operator, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            // operator must be the current character
            $items[] = $input->charAt($index);
            $index += 1;
        }

        return $items;
    }

    /**
     * @throws InvalidOperandException
     */
    protected static function parse(array $tokens): array
    {
        // TODO: this returns an array of an array
        //  the non bracketed version returns an array

        $tokens = new Collection($tokens);

        $tokens = $tokens->mapWithKeys(function(string|array $item, int $key) {
            if($key % 2 === 0) {
                if(is_array($item)) {
                    return static::parse($item);
                }

                return is_numeric($item) ? (int) $item : throw new InvalidOperandException;
            } else {
                return Operator::from($item);
            }
        });

        if($tokens[array_key_last($tokens->toArray())] instanceof Operator) {
            throw new InvalidOperandException;
        }

        $tokens = $tokens->mapWithKeys(function(int|Operator|array $item, int $key) {
            // array is for nested (bracketed) operations
            if($key % 2 === 0 and is_array($item)) {
                return new Expression(...$item);
            }

            return $item;
        });

        return $tokens->toArray();
    }

    protected static function resolve(array $expression): int
    {
        $expression = new Collection($expression);

        // until we have a single result
        while($expression->count() > 1) {
            // return the index of the most pressing operation
            // TODO: this feels like the bracket logic, should this be part of the parser?
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

        if($expression[0] instanceof Expression) {
            return $expression[0]->result();
        }

        return $expression[0];
    }
}
