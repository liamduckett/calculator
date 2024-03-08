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

        $typedTokens = static::addTypes($tokens);
        $expression = static::parse($typedTokens);

        return $expression->result();
    }

    /**
     * @throws InvalidOperandException
     * @throws InvalidOperatorException
     */
    protected static function addTypes(array $tokens): array
    {
        $tokens = new Collection($tokens);

        // assign types
        $tokens = $tokens->mapWithKeys(function(string|array $item, int $key) {
            if($key % 2 === 0) {
                if(is_array($item)) {
                    return static::addTypes($item);
                }

                return is_numeric($item) ? (int) $item : throw new InvalidOperandException;
            } else {
                return Operator::tryFrom($item) ?? throw new InvalidOperatorException;
            }
        });

        // expression is invalid if it ends with an operation?
        $lastToken = $tokens[array_key_last($tokens->toArray())];
        if($lastToken instanceof Operator) {
            throw new InvalidOperandException;
        }

        return $tokens->toArray();
    }

    protected static function parse(array $tokens): Expression
    {
        $tokens = new Collection($tokens);

        if($tokens->count() === 1 && is_array($tokens[0])) {
            return static::parse($tokens[0]);
        }

        if($tokens->count() === 1) {
            return new Expression($tokens[0]);
        }

        // Find most important operator
        // Split structure by it
        // Parse each side

        $operationIndex = static::findOperationIndex($tokens);

        $operator = $tokens->slice($operationIndex, 1)[0];

        // get the left (of this operator)
        $left = $tokens->slice(0, $operationIndex)->toArray();
        $left = static::extractOperand($left);

        // get the right (of this operator)
        $right = $tokens->slice($operationIndex + 1)->toArray();
        $right = static::extractOperand($right);

        return new Expression($left, $operator, $right);
    }

    protected static function extractOperand(array $collection): int|Expression
    {
        // if the first item of this array is an array
        // is if(if_array($collection[0])

        if(count($collection) === 1 && is_array($collection[0])) {
            return static::extractOperand($collection[0]);
        }

        // call this function on it, if it is more than one item
        return match(count($collection) === 1) {
            true => $collection[0],
            false => static::parse($collection),
        };
    }

    protected static function findOperationIndex(Collection $tokens): int
    {
        // all operators are left associative so far
        // so find the last instance, of the highest priority one...

        $index = $tokens->searchMultiple(
            [Operator::EXPONENTIATE],
            default: null,
        );

        $indexFromEnd = $tokens->reverse()->searchMultiple([
            Operator::ADD,
            Operator::SUBTRACT,
            Operator::DIVIDE,
            Operator::MULTIPLY,
        ], default: null);

        if($indexFromEnd !== null) {
            $index = count($tokens) - 1 - $indexFromEnd;
        }

        // count is 1 higher than last index
        return $index;
    }
}
