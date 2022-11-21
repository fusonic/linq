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

use InvalidArgumentException;
use Traversable;

class SelectIterator extends \IteratorIterator
{
    private $selector;

    public function __construct(Traversable $iterator, callable $selector)
    {
        parent::__construct($iterator);
        if ($selector === null) {
            throw new InvalidArgumentException("Selector must not be null.");
        }

        $this->selector = $selector;
    }
	
	#[\ReturnTypeWillChange]
    public function current()
    {
        $selector = $this->selector;
        return $selector(parent::current());
    }
}
