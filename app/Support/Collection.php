<?php

namespace Liamduckett\Calculator\Support;

use ArrayAccess;
use Countable;

class Collection implements ArrayAccess, Countable {
    /**
     * @param list<mixed> $items
     */
    function __construct(
        protected array $items,
    ) {}

    /**
     * @param list<mixed> $items
     * @return self
     */
    static function make(array $items): self
    {
        return new self($items);
    }

    /**
     * @return list<mixed>
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
     * @param callable $callable
     * @return self
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
     * @return self
     */
    function sortByKeys(): self
    {
        $items = $this->items;
        ksort($items);
        return new self($items);
    }

    /**
     * @return self
     */
    function values(): self
    {
        $items = array_values($this->items);
        return new self($items);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return self
     */
    function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        $items = array_slice($this->items, $offset, $length, $preserveKeys);
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
     * @return self
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
     * @param $offset
     * @return bool
     */
    function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param $offset
     * @return mixed
     */
    function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * @param $offset
     * @param $value
     * @return void
     */
    function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @param $offset
     * @return void
     */
    function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}
