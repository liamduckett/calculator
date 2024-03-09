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
        // TODO: constructing an Expression with just an Expression first operand shouldnt be allowed
        //  implement other constructors
        public int|Expression $firstOperand,
        public Operator|null $operator = null,
        public int|Expression|null $secondOperand = null,
    ) {}

    /**
     * @return int
     *
     * @throws InvalidExpressionException
     */
    function result(): int
    {
        if($this->operator === null && $this->secondOperand === null && !($this->firstOperand instanceof Expression))
        {
            // TODO: could this cause an issue if this is an operation
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
