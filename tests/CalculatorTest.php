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
        $input = '5 - 3';

        $output = Calculator::calculate($input);

        $this->assertSame(2, $output);
    }

    #[Test]
    function canSubtractMultiple(): void
    {
        $input = '5 - 3 - 2';

        $output = Calculator::calculate($input);

        $this->assertSame(0, $output);
    }

    #[Test]
    function canAddAndSubtract(): void
    {
        $input = '5 + 3 - 2';

        $output = Calculator::calculate($input);

        $this->assertSame(6, $output);
    }

    #[Test]
    function canMultiply(): void
    {
        $input = '5 * 3';

        $output = Calculator::calculate($input);

        $this->assertSame(15, $output);
    }

    #[Test]
    function canMultiplyAndAdd(): void
    {
        $input = '5 * 3 + 2';

        $output = Calculator::calculate($input);

        $this->assertSame(17, $output);
    }

    #[Test]
    function canAddAndMultiply(): void
    {
        $input = '5 + 3 * 2';

        $output = Calculator::calculate($input);

        $this->assertSame(11, $output);
    }
}
