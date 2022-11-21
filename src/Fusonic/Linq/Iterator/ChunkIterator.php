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
use Traversable;

/**
 * Iterates over an iterator, returning Linq objects of the given chunk size.
 */
class ChunkIterator implements \IteratorAggregate
{
	/** @var Traversable  */
    private Traversable $iterator;
    private int $chunkSize;
	
	/**
	 * @param Traversable $iterator
	 * @param int         $chunkSize
	 */
    public function __construct(Traversable $iterator, int $chunkSize)
    {
        $this->iterator = $iterator;
        $this->chunkSize = $chunkSize;
    }
	
	/**
	 * @return \Generator<Linq>
	 */
    public function getIterator(): \Generator
    {
        $chunk = [];
        $current = 0;
        foreach ($this->iterator as $d) {
            $current++;
            $chunk[] = $d;

            if ($current >= $this->chunkSize) {
                yield Linq::from($chunk);
                $chunk = [];
                $current = 0;
            }
        }
        if(count($chunk) > 0) {
            yield Linq::from($chunk);
        }
    }
}
