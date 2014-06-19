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

class IntersectIterator implements Iterator
{
    private $first;
    private $second;
    private $intersections;

    public function __construct(Iterator $first, Iterator $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function current()
    {
        return $this->intersections->current();
    }

    public function next()
    {
        $this->intersections->next();
    }

    public function key()
    {
        return $this->intersections->key();
    }

    public function valid()
    {
        return $this->intersections->valid();
    }

    public function rewind()
    {
        if ($this->intersections === null) {
            $this->calcIntersections();
        }

        $this->intersections->rewind();
    }

    private function calcIntersections()
    {
        $firstArray = iterator_to_array($this->first);
        $secondArray = iterator_to_array($this->second);
        $this->intersections = new ArrayIterator(array_intersect($firstArray, $secondArray));
    }
}
