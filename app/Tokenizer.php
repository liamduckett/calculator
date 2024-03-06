<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Support\Str;

class Tokenizer
{
    protected int $index = 0;
    protected array $tokens = [];

    public function __construct(
        protected Str $input,
    ) {}

    function run(): array
    {
        while($this->index < $this->input->length()) {
            $operand = null;

            if($this->currentCharacter()->is('(')) {
                $this->incrementCharacter();

                // loop through until we find a closed bracket
                while($this->currentCharacter()->isnt(')')) {
                    $operand .= $this->currentCharacter();
                    $this->incrementCharacter();
                }

                // skip the closed bracket
                $this->incrementCharacter();
                // recursive call for brackets
                $this->tokens[] = static::tokenize($operand);
            }

            // there may not be an operand here, this could be the end of the string...
            if($this->currentCharacterIsLastCharacter()) {
                break;
            }

            // only look for regular operand if bracketed one wasn't found
            if(!$operand) {
                $operand = '';

                while($this->currentCharacter()->isNumeric()) {
                    $operand .= $this->currentCharacter();
                    $this->incrementCharacter();
                }

                $this->tokens[] = $operand;
            }

            // there may not be an operator, this could be the end of the string...
            if($this->currentCharacterIsLastCharacter()) {
                break;
            }

            $this->skipSpaces();

            // operators are only 1 character long
            $this->tokens[] = $this->currentCharacter()->toString();
            $this->incrementCharacter();

            $this->skipSpaces();
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
        while($this->currentCharacter()->toString() === ' ') {
            $this->incrementCharacter();
        }
    }

    protected function incrementCharacter(): void
    {
        $this->index += 1;
    }

    static function tokenize(string $input): array
    {
        $input = Str::make($input)
            // allow use of 'x' as '*'
            ->replace(search: 'x', replace: '*')
            // allow use of '**' as '^'
            ->replace(search: '**', replace: '^')
            // remove whitespace either side
            ->trim();

        $tokenizer = new self($input);

        return $tokenizer->run();
    }
}
