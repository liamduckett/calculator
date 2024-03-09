<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Support\Str;

class Tokenizer
{
    protected int $index = 0;
    /** @var list<mixed> $tokens  */
    protected array $tokens = [];

    /**
     * @param string $input
     * @return list<mixed>
     */
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

    /**
     * @param Str $input
     */
    protected function __construct(
        protected Str $input,
    ) {}

    /**
     * @return list<mixed>
     */
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

    /**
     * @return Str
     */
    protected function currentCharacter(): Str
    {
        return $this->input->charAt($this->index);
    }

    /**
     * @return bool
     */
    protected function currentCharacterIsLastCharacter(): bool
    {
        return $this->index === $this->input->length();
    }

    /**
     * @return void
     */
    protected function skipSpaces(): void
    {
        // skip any spaces
        while($this->currentCharacter()->is(' ')) {
            $this->incrementCharacter();
        }
    }

    /**
     * @return void
     */
    protected function incrementCharacter(): void
    {
        $this->index += 1;
    }

    /**
     * @return void
     */
    protected function extractOperand(): void
    {
        $operand = match($this->currentCharacter()->is('(')) {
            true => $this->extractBracketedOperand(),
            false => $this->extractSimpleOperand(),
        };

        $this->tokens[] = $operand;
    }

    /**
     * @return string
     */
    protected function extractSimpleOperand(): string
    {
        $operand = '';

        while($this->currentCharacter()->isNumeric()) {
            $operand .= $this->currentCharacter();
            $this->incrementCharacter();
        }

        return $operand;
    }

    /**
     * @return list<mixed>
     */
    protected function extractBracketedOperand(): array
    {
        $operand = '';
        $openedBrackets = 1;

        $this->incrementCharacter();

        // loop through until we find our closing bracket
        // accounting for nested brackets
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
        return static::tokenize($operand);
    }

    /**
     * @return void
     */
    protected function extractOperation(): void
    {
        // operators are only 1 character long
        $this->tokens[] = $this->currentCharacter()->toString();
        $this->incrementCharacter();

        $this->skipSpaces();
    }
}
