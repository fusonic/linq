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
    private $first;
    private $second;

    public function __construct(Traversable $first, Traversable $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function getIterator()
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