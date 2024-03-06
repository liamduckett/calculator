<?php

use Liamduckett\Calculator\Calculator;
use Liamduckett\Calculator\Exceptions\InvalidOperandException;
use Liamduckett\Calculator\Exceptions\InvalidOperatorException;
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

    #[Test]
    function allowsXinPlaceOfAsterisk(): void
    {
        $input = '5 + 3 x 2';

        $output = Calculator::calculate($input);

        $this->assertSame(11, $output);
    }

    #[Test]
    function canDivide(): void
    {
        $input = '15 / 3';

        $output = Calculator::calculate($input);

        $this->assertSame(5, $output);
    }

    #[Test]
    function canDivideAndAdd(): void
    {
        $input = '15 / 3 + 2';

        $output = Calculator::calculate($input);

        $this->assertSame(7, $output);
    }

    #[Test]
    function canAddAndDivide(): void
    {
        $input = '12 + 3 / 3';

        $output = Calculator::calculate($input);

        $this->assertSame(13, $output);
    }

    #[Test]
    function rejectsMissingFirstOperand(): void
    {
        $this->expectException(InvalidOperandException::class);

        $input = ' + 5';

        Calculator::calculate($input);
    }

    #[Test]
    function rejectsMissingSecondOperand(): void
    {
        $this->expectException(InvalidOperandException::class);

        $input = '5 +';

        Calculator::calculate($input);
    }

    #[Test]
    function rejectsNonNumericOperand(): void
    {
        $this->expectException(InvalidOperandException::class);

        $input = '5 + string';

        Calculator::calculate($input);
    }

    #[Test]
    function allowsBrackets(): void
    {
        $input = '(5 + 3)';

        $output = Calculator::calculate($input);

        $this->assertSame(8, $output);
    }

    #[Test]
    function allowsBracketsTwo(): void
    {
        $input = '(5 + 3) + 2';

        $output = Calculator::calculate($input);

        $this->assertSame(10, $output);
    }

    #[Test]
    function rejectsInvalidOperand(): void
    {
        $this->expectException(InvalidOperatorException::class);

        $input = '5 £ 2';

        Calculator::calculate($input);
    }

    #[Test]
    function canExponentiate(): void
    {
        $input = '5 ^ 2';

        $output = Calculator::calculate($input);

        $this->assertSame(25, $output);
    }

    #[Test]
    function allowDoubleAsteriskAsExponentiation(): void
    {
        $input = '5 ** 2';

        $output = Calculator::calculate($input);

        $this->assertSame(25, $output);
    }

    #[Test]
    function rejectsDoubleOperandWithSpaceBetween(): void
    {
        $this->expectException(InvalidOperatorException::class);

        $input = '5 5 + 5';

        Calculator::calculate($input);
    }

    #[Test]
    function allowSpaceEitherSide(): void
    {
        $input = ' 5 + 5 ';

        $output = Calculator::calculate($input);

        $this->assertSame(10, $output);
    }

    #[Test]
    function allowsNestedBrackets(): void
    {
        $input = '((5 + 3) * 2)';

        $output = Calculator::calculate($input);

        $this->assertSame(16, $output);
    }
}
