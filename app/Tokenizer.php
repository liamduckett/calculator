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
            // remove spaces
            ->replace(search: ' ', replace: '');

        $items = [];
        $item = '';
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
                    $item .= $input->charAt($index)->toString();
                    $index += 1;
                }

                // skip the closed bracket
                $index += 1;
                // recursive call
                $operand = static::tokenize($item);
                $items[] = $operand;
                $item = '';
            }

            // there may not be an operand here, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            // only look for regular operand if bracketed one wasn't found
            if(!$operand) {
                while($input->charAt($index)->isNumeric()) {
                    $item .= $input->charAt($index)->toString();
                    $index += 1;
                }

                $items[] = $item;
                $item = '';
            }

            // there may not be an operator, this could be the end of the string...
            if($index === $input->length()) {
                break;
            }

            // operator must be the current character
            $items[] = $input->charAt($index)->toString();
            $index += 1;
        }

        return $items;
    }
}
