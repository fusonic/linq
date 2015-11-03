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

use Countable;
use Fusonic\Linq\Linq;
use Iterator;

/**
 * Iterates over an iterator, returning Linq objects of the given chunk size.
 */
class ChunkIterator implements Iterator, Countable
{
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var int
     */
    private $i = 0;

    /**
     * @var int
     */
    private $chunkSize;

    public function __construct(Iterator $iterator, $chunkSize)
    {
        $this->iterator = $iterator;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return Linq
     * @todo Should this chunking logic stay here? This might be better in `next()` but then `current()` should return
     *       the first chunk before we call `next()`. With current implementation, looping through without calling
     *       `current()` is inconsistent, hence the `count()` implementation.
     */
    public function current()
    {
        $chunk = [];
        while ($this->valid()) {
            $chunk[] = $this->iterator->current();

            if (count($chunk) < $this->chunkSize) {
                $this->iterator->next();
            } else {
                break;
            }
        }

        return new Linq($chunk);
    }

    public function next()
    {
        $this->iterator->next();
        $this->i++;
    }

    public function key()
    {
        return $this->i;
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
        $this->i = 0;
    }

    /**
     * Implemented to ensure tests pass.
     *
     * @return int
     */
    public function count()
    {
        return (int) ceil(iterator_count($this->iterator) / $this->chunkSize);
    }
}
