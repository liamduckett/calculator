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

        $mostImportantOperatorIndex = $tokens->searchMultiple(
            [Operator::EXPONENTIATE, Operator::MULTIPLY, Operator::DIVIDE],
            default: 1
        );

        $operator = $tokens->slice(1, $mostImportantOperatorIndex)[0];

        // get the left (of this operator)
        $left = $tokens->slice(0, $mostImportantOperatorIndex)->toArray();
        $left = static::extractOperand($left);

        // get the right (of this operator)
        $right = $tokens->slice($mostImportantOperatorIndex + 1)->toArray();
        $right = static::extractOperand($right);

        return new Expression($operator, $left, $right);
    }

    protected static function extractOperand(array $collection): int
    {
        // call this function on it, if it is more than one item
        return match(count($collection) === 1) {
            true => $collection[0],
            false => static::parse($collection),
        };
    }
}
