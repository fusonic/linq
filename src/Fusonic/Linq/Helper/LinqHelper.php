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

namespace Fusonic\Linq\Helper;

use ArrayIterator;
use InvalidArgumentException;
use UnexpectedValueException;

class LinqHelper
{
    const MODE_ASSERT = 'MODE_ASSERT';
    const MODE_NULL = 'MODE_NULL';

    const LINQ_ORDER_ASC = 'asc';
    const LINQ_ORDER_DESC = 'desc';

    const LINQ_ORDER_TYPE_NUMERIC = 1;
    const LINQ_ORDER_TYPE_ALPHANUMERIC = 2;
    const LINQ_ORDER_TYPE_DATETIME = 3;

    public static function getBoolOrThrowException($returned)
    {
        if (!is_bool($returned)) {
            throw new UnexpectedValueException("Return type of filter func must be boolean.");
        }
        return $returned;
    }

    public static function assertArgumentIsIterable($param, $argumentName)
    {
        if (!self::isIterable($param)) {
            throw new InvalidArgumentException("Argument must be an array, or implement either the \IteratorAggregate or \Iterator interface. ArgumentName = " . $argumentName);
        }
    }

    public static function getIteratorOrThrow($value)
    {
        if (is_array($value)) {
            return new ArrayIterator($value);
        }
        else if($value instanceof \IteratorAggregate) {
            return $value->getIterator();
        }
        else if($value instanceof \Iterator) {
            return $value;
        }

        throw new \UnexpectedValueException("Value must be an array, or implement either the \IteratorAggregate or \Iterator interface");
    }

    public static function isIterable($param)
    {
        return is_array($param)
            || $param instanceof \IteratorAggregate
            || $param instanceof \Iterator;
    }
}