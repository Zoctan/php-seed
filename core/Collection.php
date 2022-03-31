<?php

namespace App\Core;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;

final class Collection implements ArrayAccess, Iterator, Countable, JsonSerializable
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function __unset(string $key): void
    {
        unset($this->data[$key]);
    }

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

    public function rewind(): void
    {
        reset($this->data);
    }

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

    public function valid(): bool
    {
        $key = key($this->data);

        return null !== $key && false !== $key;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function keys(): array
    {
        return array_keys($this->data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function clear(): void
    {
        $this->data = [];
    }
}