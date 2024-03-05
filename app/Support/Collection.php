<?php

namespace Liamduckett\Calculator\Support;

use ArrayAccess;
use Countable;

class Collection implements ArrayAccess, Countable {
    function __construct(
        protected array $items,
    ) {}

    static function make(array $items): self
    {
        return new self($items);
    }

    function toArray(): array
    {
        return $this->items;
    }

    function count(): int
    {
        return count($this->items);
    }

    function mapWithKeys(callable $callable): self
    {
        $items = [];

        foreach($this->items as $key => $item) {
            $items[] = $callable($item, $key);
        }

        return new self($items);
    }

    function sortByKeys(): self
    {
        $items = $this->items;
        ksort($items);
        return new self($items);
    }

    function values(): self
    {
        $items = array_values($this->items);
        return new self($items);
    }

    function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}
