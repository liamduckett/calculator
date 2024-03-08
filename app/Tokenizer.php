<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Support\Str;

class Tokenizer
{
    protected int $index = 0;
    protected array $tokens = [];

    static function tokenize(string $input): array
    {
        $input = Str::make($input)
            // allow use of 'x' as '*'
            ->replace(search: 'x', replace: '*')
            // allow use of '**' as '^'
            ->replace(search: '**', replace: '^')
            // empty brackets are always 0
            ->replace(search: '()', replace :'0')
            // remove whitespace either side
            ->trim();

        $tokenizer = new self($input);

        return $tokenizer->run();
    }

    protected function __construct(
        protected Str $input,
    ) {}

    function run(): array
    {
        while($this->index < $this->input->length()) {
            $this->extractOperand();

            // there may not be an operator, this could be the end of the string...
            if($this->currentCharacterIsLastCharacter()) {
                break;
            }

            $this->skipSpaces();

            $this->extractOperation();
        }

        return $this->tokens;
    }

    protected function currentCharacter(): Str
    {
        return $this->input->charAt($this->index);
    }

    protected function currentCharacterIsLastCharacter(): bool
    {
        return $this->index === $this->input->length();
    }

    protected function skipSpaces(): void
    {
        // skip any spaces
        while($this->currentCharacter()->is(' ')) {
            $this->incrementCharacter();
        }
    }

    protected function incrementCharacter(): void
    {
        $this->index += 1;
    }

    protected function extractOperand(): void
    {
        // only extract an operand if we didn't find a bracketed one already
        $operand = $this->extractBracketedExpressionIfApplicable();

        if($operand === null) {
            $operand = '';

            while($this->currentCharacter()->isNumeric()) {
                $operand .= $this->currentCharacter();
                $this->incrementCharacter();
            }
        }


        $this->tokens[] = $operand;
    }

    // TODO: maybe we do this without the ifApplicable
    //  only run the function if we need to
    protected function extractBracketedExpressionIfApplicable(): ?array
    {
        $operand = null;

        if($this->currentCharacter()->is('(')) {
            $openedBrackets = 1;

            $this->incrementCharacter();

            // loop through until we find our closing bracket
            while($openedBrackets > 0) {
                $operand .= $this->currentCharacter();

                if($this->currentCharacter()->is('(')) {
                    $openedBrackets += 1;
                }

                if($this->currentCharacter()->is(')')) {
                    $openedBrackets -= 1;
                }

                $this->incrementCharacter();
            }

            // remove the closing bracket
            $operand = substr($operand, 0, -1);

            // recursive call for brackets
            $operand = static::tokenize($operand);
        }

        return $operand;
    }

    protected function extractOperation(): void
    {
        // operators are only 1 character long
        $this->tokens[] = $this->currentCharacter()->toString();
        $this->incrementCharacter();

        $this->skipSpaces();
    }
}
