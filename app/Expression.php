<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Exceptions\InvalidExpressionException;

class Expression
{
    /**
     * @param int|Expression $firstOperand
     * @param Operator|null $operator
     * @param int|Expression|null $secondOperand
     */
    function __construct(
        public int|Expression $firstOperand,
        public Operator|null $operator,
        public int|Expression|null $secondOperand,
    ) {}

    static function make(int|Expression $firstOperand, Operator $operator, int|Expression $secondOperand): self
    {
        return new self($firstOperand, $operator, $secondOperand);
    }

    static function makeSimple(int $firstOperand): self
    {
        return new self($firstOperand, null, null);
    }

    /**
     * @return int
     *
     * @throws InvalidExpressionException
     */
    function result(): int
    {
        // if this is a simple expression
        if($this->operator === null && $this->secondOperand === null && gettype($this->firstOperand) === 'integer')
        {
            return $this->firstOperand;
        }

        // https://github.com/phpstan/phpstan/issues/10585
        $first = match(gettype($this->firstOperand)) {
            'object' => $this->firstOperand->result(),
            'integer' => $this->firstOperand,
        };

        $second = match(gettype($this->secondOperand)) {
            'object' => $this->secondOperand->result(),
            'integer' => $this->secondOperand,
            'NULL' => throw new InvalidExpressionException,
        };

        return $this->operator->calculate($first, $second);
    }
}
