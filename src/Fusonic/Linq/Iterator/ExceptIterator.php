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

final class ExceptIterator implements \IteratorAggregate
{
	/** @var Traversable  */
    private Traversable $first;
	/** @var Traversable  */
    private Traversable $second;
	
	/**
	 * @param Traversable $first
	 * @param Traversable $second
	 */
    public function __construct(Traversable $first, Traversable $second)
    {
        $this->first = $first;
        $this->second = $second;
    }
	
	/**
	 * @return \Generator
	 */
    public function getIterator(): \Generator
    {
        $set = new Set();
        foreach($this->second as $second) {
            $set->add($second);
        }
        foreach($this->first as $first) {
            if(!$set->contains($first)) {
                yield $first;
            }
        }
    }
}
