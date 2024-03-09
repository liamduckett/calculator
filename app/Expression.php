<?php

namespace Liamduckett\Calculator;

class Expression
{
    /**
     * @param int|Expression $firstOperand
     * @param Operator|null $operator
     * @param int|Expression|null $secondOperand
     */
    function __construct(
        public int|Expression $firstOperand,
        public Operator|null $operator = null,
        public int|Expression|null $secondOperand = null,
    ) {}

    /**
     * @return int
     */
    function result(): int
    {
        if($this->operator === null && $this->secondOperand === null)
        {
            // TODO: could this cause an issue if this is an operation
            return $this->firstOperand;
        }

        $first = match($this->firstOperand instanceof Expression) {
            true => $this->firstOperand->result(),
            false => $this->firstOperand,
        };

        $second = match($this->secondOperand instanceof Expression) {
            true => $this->secondOperand->result(),
            false => $this->secondOperand,
        };

        return $this->operator->calculate($first, $second);
    }
}
