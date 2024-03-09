<?php

namespace Liamduckett\Calculator\Support;

use Stringable;

class Str implements Stringable
{
    /**
     * @param string $value
     */
    function __construct(
       protected string $value,
    ) {}

    /**
     * @param string $value
     * @return self
     */
    static function make(string $value): self
    {
        return new self($value);
    }

    /**
     * @return int
     */
    function length(): int
    {
        return strlen($this->value);
    }

    /**
     * @return bool
     */
    function isNumeric(): bool
    {
        return is_numeric($this->value);
    }

    /**
     * @param int $index
     * @return self
     */
    function charAt(int $index): self
    {
        $value = $this->value[$index];
        return new self($value);
    }

    /**
     * @param string $search
     * @param string $replace
     * @return self
     */
    function replace(string $search, string $replace): self
    {
        $value = str_replace($search, $replace, $this->value);
        return new self($value);
    }

    /**
     * @param string $pattern
     * @param int $limit
     * @param int $flags
     * @return Collection
     */
    function pregSplit(string $pattern, int $limit = -1, int $flags = 0): Collection
    {
        $result = preg_split($pattern, $this->value, $limit, $flags);
        // failure should return an empty array instead
        $result = $result ?: [];

        return Collection::make($result);
    }

    /**
     * @return self
     */
    function trim(): self
    {
        $value = trim($this->value);
        return new self($value);
    }

    /**
     * @param string $value
     * @return bool
     */
    function is(string $value): bool
    {
        return $this->toString() === $value;
    }

    /**
     * @param string $value
     * @return bool
     */
    function isnt(string $value): bool
    {
        return !$this->is($value);
    }

    /**
     * @return string
     */
    function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    function __toString(): string
    {
        return $this->toString();
    }
}
