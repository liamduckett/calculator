<?php

namespace Liamduckett\Calculator;

class Expression
{
    function __construct(
        public Operator $operator,
        public int|Expression $firstOperand,
        public int|Expression $secondOperand,
    ) {}

    function result(): int
    {
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
