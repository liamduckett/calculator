<?php

use Liamduckett\Calculator\Calculator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    #[Test]
    function canAddTwoNumbers(): void
    {
        $input = '5+3';

        $output = Calculator::calculate($input);

        $this->assertSame(8, $output);
    }
}
