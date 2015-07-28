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
    private $orderedIterator;
    private $orderFuncs = array();

    public function __construct(Iterator $items, $orderKeyFunc, $direction)
    {
        $this->iterator = $items;
        $this->orderFuncs[] = [
            'func' => $orderKeyFunc,
            'direction' => $direction
        ];
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
        $itemIterator = $this->iterator;
        $itemIterator->rewind();
        if (!$itemIterator->valid()) {
            $this->orderedIterator = new ArrayIterator();
            return;
        }

        $this->orderedIterator = $this->iterator;
        $this->orderedIterator->uasort(function($a, $b) { return $this->sort($a, $b); });
    }

    private function sort($a, $b)
    {
        $result = 0;
        foreach ($this->orderFuncs as &$orderFunc) {
            $func = $orderFunc['func'];

            if ($orderFunc['direction'] === Helper\LinqHelper::LINQ_ORDER_ASC) {
                $result = $this->compare($func($a), $func($b));
            } else {
                $result = $this->compare($func($b), $func($a));
            }

            if ($result !== 0) {
                break;
            }
        }

        return $result;
    }

    private function compare($a, $b)
    {
        if(is_string($a) && is_string($b))
        {
            return strcasecmp($a, $b);
        }
        else
        {
            if($a == $b) return 0;
            return $a < $b ? -1 : 1;
        }
    }

    public function thenBy($func, $direction)
    {
        $this->orderFuncs[] = [
            "func" => $func,
            "direction" => $direction,
        ];
    }
}
