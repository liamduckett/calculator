<?php

use Liamduckett\Calculator\Calculator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    #[Test]
    function canAdd(): void
    {
        $input = '5+3';

        $output = Calculator::calculate($input);

        $this->assertSame(8, $output);
    }

    #[Test]
    function allowsSpaces(): void
    {
        $input = '5 + 3';

        $output = Calculator::calculate($input);

        $this->assertSame(8, $output);
    }

    #[Test]
    function allowsThreeOperands(): void
    {
        $input = '5 + 3 + 2';

        $output = Calculator::calculate($input);

        $this->assertSame(10, $output);
    }

    #[Test]
    function canSubtract(): void
    {
        $input = '5-3';

        $output = Calculator::calculate($input);

        $this->assertSame(2, $output);
    }
}
