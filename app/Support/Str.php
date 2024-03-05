<?php

namespace Liamduckett\Calculator\Support;

use Stringable;

class Str implements Stringable
{
    function __construct(
       protected string $value,
    ) {}

    static function make(string $value): self
    {
        return new self($value);
    }

    function replace(string $search, string $replace): self
    {
        $value = str_replace($search, $replace, $this->value);
        return new self($value);
    }

    function pregSplit(string $pattern, int $limit = -1, int $flags = 0): Collection
    {
        $result = preg_split($pattern, $this->value, $limit, $flags);
        // failure should return an empty array instead
        $result = $result ?: [];

        return Collection::make($result);
    }

    function __toString(): string
    {
        return $this->value;
    }
}
