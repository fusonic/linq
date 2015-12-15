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
use IteratorAggregate;
use Traversable;

class WhereIterator implements IteratorAggregate
{
    private $func;
    private $inner;

    public function __construct(Traversable $inner, callable $func)
    {
        $this->inner = $inner;
        $this->func = $func;
    }

    public function getIterator()
    {
        $func = $this->func;
        foreach($this->inner as $current) {
            $accept = Helper\LinqHelper::getBoolOrThrowException($func($current));
            if($accept) {
                yield $current;
            }
        }
    }
}