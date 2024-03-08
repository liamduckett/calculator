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
        echo "input: $input" . PHP_EOL;
        $tokens = Tokenizer::tokenize($input);

        $typedTokens = static::addTypes($tokens);
        $expression = static::parse($typedTokens);

        var_dump($expression);

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
        $tokens = $tokens->mapWithKeys(function(string $item, int $key) {
            return match($key % 2 === 0) {
                true => is_numeric($item) ? (int) $item : throw new InvalidOperandException,
                false => Operator::tryFrom($item) ?? throw new InvalidOperatorException,
            };
        });

        return $tokens->toArray();
    }

    protected static function parse(array $tokens): Expression
    {
        $tokens = new Collection($tokens);

        if($tokens->count() === 1) {
            return $tokens[0];
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

        return new Expression($operator, $left, $right);
    }

    protected static function extractOperand(array $collection): int|Expression
    {
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

        $indexFromEnd = $tokens->reverse()->searchMultiple([
            Operator::ADD,
            Operator::SUBTRACT,
            Operator::DIVIDE,
            Operator::MULTIPLY,
            Operator::EXPONENTIATE
        ]);

        // count is 1 higher than last index
        return count($tokens) - 1 - $indexFromEnd;
    }
}
