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

use Fusonic\Linq\Helper\Set;
use Traversable;

final class DistinctIterator implements \IteratorAggregate
{
	/** @var Traversable  */
    private Traversable $iterator;
	
	/**
	 * @param Traversable $iterator
	 */
    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
    }
	
	/**
	 * @return \Generator
	 */
    public function getIterator(): \Generator
    {
        $set = new Set();
        foreach($this->iterator as $value) {
            if($set->add($value)) {
                yield $value;
            }
        }
    }
}
