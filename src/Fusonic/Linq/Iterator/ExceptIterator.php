<?php

/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Iterator;

use ArrayIterator;
use Iterator;

class ExceptIterator implements Iterator
{
    private $first;
    private $second;
    private $result;

    public function __construct(Iterator $first, Iterator $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function current()
    {
        return $this->result->current();
    }

    public function next()
    {
        $this->result->next();
    }

    public function key()
    {
        return $this->result->key();
    }

    public function valid()
    {
        return $this->result->valid();
    }

    public function rewind()
    {
        if ($this->result === null) {
            $this->getResult();
        }

        $this->result->rewind();
    }

    private function getResult()
    {
        $firstArray = iterator_to_array($this->first);
        $secondArray = iterator_to_array($this->second);
        $this->result = new ArrayIterator(array_diff($firstArray, $secondArray));
    }
}
