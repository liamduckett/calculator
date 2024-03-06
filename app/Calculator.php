<?php

namespace Liamduckett\Calculator;

use Exception;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Exceptions\InvalidOperatorException;
use Liamduckett\Calculator\Support\Collection;

class Calculator
{
    /**
     * @throws Exception
     */
    static function calculate(string $input): int
    {
        $tokens = Tokenizer::tokenize($input);

        $expression = static::parse($tokens);

        return static::resolve($expression);
    }

    /**
     * @throws InvalidOperandException
     * @throws InvalidOperatorException
     */
    protected static function parse(array $tokens): array
    {
        $tokens = new Collection($tokens);

        $tokens = $tokens->mapWithKeys(function(string|array $item, int $key) {
            if($key % 2 === 0) {
                if(is_array($item)) {
                    if(empty($item)) {
                        throw new InvalidOperandException;
                    }

                    return static::parse($item);
                }

                return is_numeric($item) ? (int) $item : throw new InvalidOperandException;
            } else {
                return Operator::tryFrom($item) ?? throw new InvalidOperatorException;
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
            $operatorIndex = $expression->searchMultiple(
                [Operator::EXPONENTIATE, Operator::MULTIPLY, Operator::DIVIDE],
                default: 1
            );

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
