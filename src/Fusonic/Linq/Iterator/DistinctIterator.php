<?php

namespace Fusonic\Linq\Iterator;

use Iterator;

class DistinctIterator extends \IteratorIterator
{
    private $iterator;
    private $distinct;

    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function current()
    {
        return $this->distinct->current();
    }

    public function next()
    {
        $this->distinct->next();
    }

    public function key()
    {
        return $this->distinct->key();
    }

    public function valid()
    {
        return $this->distinct->valid();
    }

    public function rewind()
    {
        if ($this->distinct === null)
        {
            $this->getDistincts();
        }

        $this->distinct->rewind();
    }

    private function getDistincts()
    {
        $data = iterator_to_array($this->iterator);
        $distinct = array_unique($data);
        $this->distinct = new \ArrayIterator($distinct);
    }
}