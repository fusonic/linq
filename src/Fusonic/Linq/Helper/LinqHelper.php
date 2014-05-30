<?php
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

    public static function isTraversable($param)
    {
        return is_array($param) || $param instanceof \Traversable;
    }

    public static function assertIsTraversable($param, $argumentName)
    {
        if (!self::isTraversable($param)) {
            throw new InvalidArgumentException("Argument must be an array or implementing the Traversable interface. ArgumentName = " . $argumentName);
        }
    }

    public static function getTraversableOrThrow($value)
    {
        if (!self::isTraversable($value)) {
            throw new \UnexpectedValueException("Value must be an array or implementing the Traversable interface.");
        }

        if (is_array($value)) {
            $value = new ArrayIterator($value);
        }

        return $value;
    }
}
