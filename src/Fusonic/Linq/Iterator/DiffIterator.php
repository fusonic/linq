<?php

namespace Fusonic\Linq\Iterator;

use ArrayIterator;
use Iterator;

class DiffIterator implements Iterator
{
    private $first, $second;
    private $diffs;

    public function __construct(Iterator $first, Iterator $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function current()
    {
        return $this->diffs->current();
    }

    public function next()
    {
        $this->diffs->next();
    }

    public function key()
    {
        return $this->diffs->key();
    }

    public function valid()
    {
        return $this->diffs->valid();
    }

    public function rewind()
    {
        if ($this->diffs === null)
        {
            $this->getDiffs();
        }

        $this->diffs->rewind();
    }

    private function getDiffs()
    {
        $firstArray = iterator_to_array($this->first);
        $secondArray = iterator_to_array($this->second);
        $this->diffs = new ArrayIterator(array_diff($firstArray, $secondArray));
    }
}