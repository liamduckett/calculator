<?php

namespace Liamduckett\Calculator\Support;

use ArrayAccess;
use Countable;

/**
 * @template TValue
 * @implements ArrayAccess<int, TValue>
 */
class Collection implements ArrayAccess, Countable {
    /**
     * @param list<TValue> $items
     */
    function __construct(
        protected array $items,
    ) {}

    /**
     * @param list<TValue> $items
     * @return self<TValue>
     */
    static function make(array $items): self
    {
        return new self($items);
    }

    /**
     * @return list<TValue>
     */
    function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    function count(): int
    {
        return count($this->items);
    }

    /**
     * @return TValue
     */
    function first(): mixed
    {
        $items = $this->items;

        return $items[array_key_first($items)];
    }

    /**
     * @return TValue
     */
    function last(): mixed
    {
        $items = $this->items;

        return $items[array_key_last($items)];
    }

    /**
     * @param callable $callable
     * @return self<TValue>
     */
    function mapWithKeys(callable $callable): self
    {
        $items = [];

        foreach($this->items as $key => $item) {
            $items[] = $callable($item, $key);
        }

        return new self($items);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return self<TValue>
     */
    function slice(int $offset, ?int $length = null): self
    {
        $items = array_slice($this->items, $offset, $length);
        return new self($items);
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @return false|int|string
     */
    function search(mixed $needle, bool $strict = true): false|int|string
    {
        return array_search($needle, $this->items, $strict);
    }

    /**
     * @return self<TValue>
     */
    function reverse(): self
    {
        $values = array_reverse($this->items);
        return new self($values);
    }

    /**
     * @param list<mixed> $needles
     * @param bool $strict
     * @param mixed $default
     * @return mixed
     */
    function searchMultiple(array $needles, bool $strict = true, mixed $default = false): mixed
    {
        foreach($needles as $needle)
        {
            $search = array_search($needle, $this->items, $strict);

            if($search !== false) {
                return $search;
            }
        }

        return $default;
    }

    /**
     * @param int $offset
     * @return bool
     */
    function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param int $offset
     * @return mixed
     */
    function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * @param int $offset
     * @param TValue $value
     * @return void
     */
    function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param int $offset
     * @return void
     */
    function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}
