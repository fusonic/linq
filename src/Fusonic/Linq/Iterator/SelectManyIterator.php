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

use Fusonic\Linq\Helper;
use Traversable;

class SelectManyIterator implements \IteratorAggregate
{
    private $iterator;

    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    public function getIterator()
    {
        foreach($this->iterator as $current) {
            Helper\LinqHelper::assertValueIsIterable($current);
            foreach($current as $value) {
                yield $value;
            }
        }
    }
}