<?php

namespace Liamduckett\Calculator;

use Liamduckett\Calculator\Support\Str;

class Tokenizer
{
    static function tokenize(string $input): array
    {
        $input = Str::make($input)
            // allow use of 'x' as '*'
            ->replace(search: 'x', replace: '*')
            // allow use of '**' as '^'
            ->replace(search: '**', replace: '^')
            // remove whitespace either side
            ->trim();

        $tokens = [];
        $index = 0;

        // Loop through the string
        // Look for operand, until a non-numeric character is found
        // Next character HAS to be an operation

        while($index < $input->length()) {
            $operand = null;

            // if we have an open bracket
            if($input->charAt($index)->toString() === '(') {
                $index += 1;

                // loop through until we find a closed bracket
                while($input->charAt($index)->toString() !== ')') {
                    $operand .= $input->charAt($index)->toString();
                    $index += 1;
                }

                // skip the closed bracket
                $index += 1;
                // recursive call
                $tokens[] = static::tokenize($operand);
            }

            // there may not be an operand here, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            // only look for regular operand if bracketed one wasn't found
            if(!$operand) {
                $operand = '';

                while($input->charAt($index)->isNumeric()) {
                    $operand .= $input->charAt($index)->toString();
                    $index += 1;
                }

                $tokens[] = $operand;
            }

            // there may not be an operator, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            // skip any spaces
            while($input->charAt($index)->toString() === ' ') {
                $index += 1;
            }

            // operators are only 1 character long
            $tokens[] = $input->charAt($index)->toString();
            $index += 1;

            // skip any spaces
            while($input->charAt($index)->toString() === ' ') {
                $index += 1;
            }
        }

        return $tokens;
    }
}
