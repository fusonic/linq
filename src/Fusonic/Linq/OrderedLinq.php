<?php

/*
 * This file is part of Fusonic-linq.
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq;

use Fusonic\Linq\Helper\LinqHelper;
use Fusonic\Linq\Iterator\OrderIterator;

/**
 * Class OrderedLinq
 * Represents a Linq object that is sorted.
 * @package Fusonic\Linq
 */
class OrderedLinq extends Linq
{
    /** @var  OrderIterator */
    protected $iterator;

    public function __construct($dataSource)
    {
        parent::__construct($dataSource);
    }

    /**
     * Performs a subsequent ordering of the elements in a sequencing in ascending order according to the key provided by $func.
     *
     * @param callback $func    A function to extract a key from an element.
     * @return OrderedLinq             A new Linq instance whose elements are sorted ascending according to a key.
     */
    public function thenBy($func)
    {
        $this->iterator->thenBy($func, LinqHelper::LINQ_ORDER_ASC);

        return $this;
    }

    /**
     * Performs a subsequent ordering of the elements in a sequencing in descending order according to the key provided by $func.
     *
     * @param callback $func    A function to extract a key from an element.
     * @return OrderedLinq             A new Linq instance whose elements are sorted ascending according to a key.
     */
    public function thenByDescending($func)
    {
        $this->iterator->thenBy($func, LinqHelper::LINQ_ORDER_DESC);

        return $this;
    }
}