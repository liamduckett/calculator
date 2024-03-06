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

    function length(): int
    {
        return strlen($this->value);
    }

    function isNumeric(): bool
    {
        return is_numeric($this->value);
    }

    function charAt(int $index): self
    {
        $value = $this->value[$index];
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

    function trim(): self
    {
        $value = trim($this->value);
        return new self($value);
    }

    function is(string $value): bool
    {
        return $this->toString() === $value;
    }

    function isnt(string $value): bool
    {
        return !$this->is($value);
    }

    function toString(): string
    {
        return $this->value;
    }

    function __toString(): string
    {
        return $this->toString();
    }
}
