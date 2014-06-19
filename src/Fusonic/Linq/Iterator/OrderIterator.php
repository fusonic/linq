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
use ArrayIterator;
use Fusonic\Linq;
use Fusonic\Linq\Helper;

class OrderIterator implements Iterator
{
    private $iterator;
    private $direction;
    private $orderedIterator;
    private $orderKeyFunc;

    public function __construct(Iterator $items, $orderKeyFunc, $direction)
    {
        $this->iterator = $items;
        $this->direction = $direction;
        $this->orderKeyFunc = $orderKeyFunc;
    }

    public function current()
    {
        return $this->orderedIterator->current();
    }

    public function next()
    {
        $this->orderedIterator->next();
    }

    public function key()
    {
        return $this->orderedIterator->key();
    }

    public function valid()
    {
        return $this->orderedIterator->valid();
    }

    public function rewind()
    {
        if ($this->orderedIterator == null) {
            $this->orderItems();
        }
        $this->orderedIterator->rewind();
    }

    public function orderItems()
    {
        $orderKeyFunc = $this->orderKeyFunc;
        $direction = $this->direction;

        $itemIterator = $this->iterator;
        $itemIterator->rewind();
        if (!$itemIterator->valid()) {
            $this->orderedIterator = new ArrayIterator();
            return;
        }

        $firstOrderKey = $orderKeyFunc($itemIterator->current());

        $sortType = Helper\LinqHelper::LINQ_ORDER_TYPE_NUMERIC;

        if ($firstOrderKey instanceof \DateTime) {
            $sortType = Helper\LinqHelper::LINQ_ORDER_TYPE_DATETIME;
        } elseif (!is_numeric($firstOrderKey)) {
            $sortType = Helper\LinqHelper::LINQ_ORDER_TYPE_ALPHANUMERIC;
        }

        $keyMap = array();
        $valueMap = array();

        foreach ($itemIterator as $value) {
            $orderKey = $orderKeyFunc != null ? $orderKeyFunc($value) : $value;
            if ($sortType == Helper\LinqHelper::LINQ_ORDER_TYPE_DATETIME) {
                $orderKey = $orderKey->getTimeStamp();
            }
            $keyMap[] = $orderKey;
            $valueMap[] = $value;
        }

        if ($sortType == Helper\LinqHelper::LINQ_ORDER_TYPE_DATETIME) {
            $sortType = Helper\LinqHelper::LINQ_ORDER_TYPE_NUMERIC;
        }

        if ($direction == Helper\LinqHelper::LINQ_ORDER_ASC) {
            asort($keyMap, $sortType == Helper\LinqHelper::LINQ_ORDER_TYPE_NUMERIC ? SORT_NUMERIC : SORT_LOCALE_STRING);
        } else {
            arsort($keyMap, $sortType == Helper\LinqHelper::LINQ_ORDER_TYPE_NUMERIC ? SORT_NUMERIC : SORT_LOCALE_STRING);
        }

        $sorted = new ArrayIterator(array());
        foreach ($keyMap as $key => $value) {
            $sorted[] = $valueMap[$key];
        }

        $this->orderedIterator = $sorted;
    }
}
