<?php

/**
 * @property array x
 */
class TestIteratorAggregate implements \IteratorAggregate
{
    public function __construct(array $x)
    {
        $this->x = $x;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->x);
    }
}
