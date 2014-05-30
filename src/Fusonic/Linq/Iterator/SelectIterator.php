<?php

namespace Fusonic\Linq\Iterator;

use InvalidArgumentException;
use Iterator;

class SelectIterator extends \IteratorIterator
{
    private $selector;

    public function __construct(Iterator $iterator, $selector)
    {
        parent::__construct($iterator);
        if ($selector === null) {
            throw new InvalidArgumentException("Selector must not be null.");
        }

        $this->selector = $selector;
    }

    public function current()
    {
        $selector = $this->selector;
        return $selector(parent::current());
    }
}
