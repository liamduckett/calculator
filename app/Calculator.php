<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Exceptions\InvalidExpressionException;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Exceptions\InvalidOperatorException;
use Liamduckett\Calculator\Support\Collection;

class Calculator
{
    /**
     * @param string $input
     * @return int
     *
     * @throws InvalidExpressionException
     * @throws InvalidOperandException
     * @throws InvalidOperatorException
     */
    static function calculate(string $input): int
    {
        $tokens = Tokenizer::tokenize($input);

        $typedTokens = static::addTypes($tokens);
        $expression = static::parse($typedTokens);

        return $expression->result();
    }

    /**
     * @param list<mixed> $tokens
     * @return list<mixed>
     *
     * @throws InvalidOperandException
     * @throws InvalidOperatorException
     */
    protected static function addTypes(array $tokens): array
    {
        $tokens = new Collection($tokens);

        // assign types
        $tokens = $tokens->mapWithKeys(function(string|array $item, int $key) {
            return match($key % 2 === 0) {
                true => static::addTypesToOperand($item),
                /** @phpstan-ignore-next-line $item is always a string here (tokenizer) */
                false => static::addTypesToOperator($item),
            };
        });

        // expression is invalid if it ends with an operation
        if($tokens->last() instanceof Operator) {
            throw new InvalidOperandException;
        }

        return $tokens->toArray();
    }

    /**
     * @param string|list<mixed> $operand
     * @return int|list<mixed>
     *
     * @throws InvalidOperatorException
     * @throws InvalidOperandException
     */
    protected static function addTypesToOperand(string|array $operand): int|array
    {
        // if the operand is not simply a number, we need to call this recursively
        if(is_array($operand)) {
            return static::addTypes($operand);
        }

        return is_numeric($operand) ? (int) $operand : throw new InvalidOperandException;
    }

    /**
     * @param string $operator
     * @return Operator
     *
     * @throws InvalidOperatorException
     */
    protected static function addTypesToOperator(string $operator): Operator
    {
        return Operator::tryFrom($operator) ?? throw new InvalidOperatorException;
    }

    /**
     * @param list<mixed> $tokens
     * @return Expression
     */
    protected static function parse(array $tokens): Expression
    {
        $tokens = new Collection($tokens);

        // if the tokens contain nesting, we need to call this recursively
        if($tokens->count() === 1 && is_array($tokens[0])) {
            return static::parse($tokens[0]);
        }

        // if the tokens are just one item
        // create a simple expression from it
        if($tokens->count() === 1) {
            return Expression::makeSimple($tokens[0]);
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

        return Expression::make($left, $operator, $right);
    }

    /**
     * @param list<mixed> $operand
     * @return Expression
     */
    protected static function extractOperand(array $operand): Expression
    {
        // if this has one item, and it is an array
        if(count($operand) === 1 && is_array($operand[0])) {
            return static::extractOperand($operand[0]);
        }

        // call this function on it, if it is more than one item
        return match(count($operand) === 1) {
            true => Expression::makeSimple($operand[0]),
            false => static::parse($operand),
        };
    }

    /**
     * @param Collection<mixed> $tokens
     * @return int
     */
    protected static function findOperationIndex(Collection $tokens): int
    {
        // priority is the reverse of BIDMAS
        // because we are assembling (to run in the opposite order)

        // exponentiation is right associative, so need to find the first one
        $index = $tokens->searchMultiple(
            [Operator::EXPONENTIATE],
            default: null,
        );

        // these operations are left associative, so need to find the last one
        // (of the lowest priority)
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
