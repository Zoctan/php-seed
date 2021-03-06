<?php

namespace App\Util;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;

/**
 * Array wrap
 */
final class Collection implements ArrayAccess, Iterator, Countable, JsonSerializable
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function unset(string $key): void
    {
        unset($this->data[$key]);
    }

    public function keyExists($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function keys(): array
    {
        return array_keys($this->data);
    }

    public function values(): array
    {
        return array_values($this->data);
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function clearData(): void
    {
        $this->data = [];
    }

    // ----------------------- ArrayAccess ---------------------------------
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    // ----------------------- Iterator ---------------------------------
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->data);
    }

    public function rewind(): void
    {
        reset($this->data);
    }

    public function valid(): bool
    {
        $key = key($this->data);

        return null !== $key && false !== $key;
    }

    // ----------------------- Countable ---------------------------------
    public function count(): int
    {
        return count($this->data);
    }

    // ----------------------- JsonSerializable ---------------------------------
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
