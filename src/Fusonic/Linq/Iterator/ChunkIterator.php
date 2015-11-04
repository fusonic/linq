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

use Fusonic\Linq\Linq;
use Iterator;

/**
 * Iterates over an iterator, returning Linq objects of the given chunk size.
 */
class ChunkIterator implements Iterator
{
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $chunk;

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
     */
    public function current()
    {
        return new Linq($this->chunk);
    }

    public function next()
    {
        $this->iterator->next();
        $this->chunk = $this->getNextChunk();
        $this->i++;
    }

    private function getNextChunk()
    {
        $chunk = [];
        while ($this->iterator->valid()) {
            $chunk[] = $this->iterator->current();

            if (count($chunk) < $this->chunkSize) {
                $this->iterator->next();
            } else {
                break;
            }
        }

        return $chunk;
    }

    public function key()
    {
        return $this->i;
    }

    public function valid()
    {
        return !empty($this->chunk);
    }

    public function rewind()
    {
        $this->iterator->rewind();
        $this->i = 0;
        $this->chunk = $this->getNextChunk();
    }
}
