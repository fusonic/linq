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

use Iterator;
use Fusonic\Linq\Helper;

class SelectManyIterator implements Iterator
{
    private $iterator;
    private $currentIterator;
    private $key = 0;

    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function current()
    {
        if ($this->currentIterator != null) {
            return $this->currentIterator->current();
        }

        return null;
    }

    public function next()
    {
        if ($this->currentIterator != null) {
            $this->currentIterator->next();

            if (!$this->currentIterator->valid()) {
                $this->iterator->next();
                if ($this->iterator->valid()) {
                    $this->currentIterator = Helper\LinqHelper::getIteratorOrThrow($this->iterator->current());
                    if ($this->currentIterator != null) {
                        $this->currentIterator->rewind();
                        $this->key++;
                    }
                }
            } else {
                $this->key++;
            }
        }
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        $current = $this->currentIterator;
        return $current != null && $current->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
        if ($this->iterator->valid()) {
            $current = $this->iterator->current();
            $this->currentIterator = Helper\LinqHelper::getIteratorOrThrow($current);
            if ($this->currentIterator != null) {
                $this->currentIterator->rewind();
            }
        } else {
            $this->currentIterator = null;
        }

        $this->key = 0;
    }
}