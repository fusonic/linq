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

use Countable;
use Fusonic\Linq\Iterator\ExceptIterator;
use Fusonic\Linq\Iterator\DistinctIterator;
use Fusonic\Linq\Iterator\GroupIterator;
use Fusonic\Linq\Iterator\IntersectIterator;
use Fusonic\Linq\Iterator\OfTypeIterator;
use Fusonic\Linq\Iterator\OrderIterator;
use Fusonic\Linq\Iterator\SelectIterator;
use Fusonic\Linq\Iterator\SelectManyIterator;
use Fusonic\Linq\Iterator\WhereIterator;
use Fusonic\Linq\Helper\LinqHelper;
use IteratorAggregate;
use Traversable;
use UnexpectedValueException;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Linq is a simple, powerful and consistent library for querying, projecting and aggregating data in php.
 *
 * @author David Roth <david.roth@fusonic.net>.
 */
class Linq implements IteratorAggregate, Countable
{
    private $iterator;

    /**
     * Creates a new Linq object using the provided dataSource.
     *
     * @param array|\Iterator|IteratorAggregate $dataSource     A Traversable sequence as data source.
     */
    public function __construct($dataSource)
    {
        LinqHelper::assertArgumentIsIterable($dataSource, "dataSource");
        $dataSource = LinqHelper::getIteratorOrThrow($dataSource);

        $this->iterator = $dataSource;
    }

    /**
     * Creates a new Linq object using the provided dataDataSource.
     * This is the recommended way for getting a new Linq instance.
     *
     * @param array|\Iterator|IteratorAggregate $dataSource     A Traversable sequence as data source.
     * @return Linq
     */
    public static function from($dataSource)
    {
        return new Linq($dataSource);
    }

    /**
     * Generates a sequence of integral numbers within a specified range.
     *
     * @param $start    The value of the first integer in the sequence.
     * @param $count    The number of sequential integers to generate.
     * @return Linq    An sequence that contains a range of sequential int numbers.
     * @throws \OutOfRangeException
     */
    public static function range($start, $count)
    {
        if ($count < 0) {
            throw new OutOfRangeException('$count must be not be negative.');
        }

        return new Linq(range($start, $start + $count - 1));
    }

    /**
     * Filters the Linq object according to func return result.
     *
     * @param callback $func    A func that returns boolean
     * @return Linq             Filtered results according to $func
     */
    public function where($func)
    {
        return new Linq(new WhereIterator($this->iterator, $func));
    }

	/**
	 * Filters the Linq object according to type.
	 *
	 * @param string $type
	 *
	 * @return Linq Filtered results according to $func
	 */
	public function ofType($type)
	{
		return new Linq(new OfTypeIterator($this->iterator, $type));
	}

    /**
     * Bypasses a specified number of elements and then returns the remaining elements.
     *
     * @param int $count    The number of elements to skip before returning the remaining elements.
     * @return Linq         A Linq object that contains the elements that occur after the specified index.
     */
    public function skip($count)
    {
        // If its an array iterator we must check the arrays bounds are greater than the skip count.
        // This is because the LimitIterator will use the seek() method which will throw an exception if $count > array.bounds.
        $innerIterator = $this->iterator;
        if ($innerIterator instanceof \ArrayIterator) {
            if ($count >= $innerIterator->count()) {
                return new Linq(array());
            }
        }

        return new Linq(new \LimitIterator($innerIterator, $count, -1));
    }

    /**
     * Returns a specified number of contiguous elements from the start of a sequence
     *
     * @param int $count    The number of elements to return.
     * @return  Linq        A Linq object that contains the specified number of elements from the start.
     */
    public function take($count)
    {
        if ($count == 0) {
            return new Linq(array());
        }

        return new Linq(new \LimitIterator($this->iterator, 0, $count));
    }

    /**
     * Applies an accumulator function over a sequence.
     * The aggregate method makes it simple to perform a calculation over a sequence of values.
     * This method works by calling $func one time for each element.
     * The first element of source is used as the initial aggregate value if $seed parameter is not specified.
     * If $seed is specified, this value will be used as the first value.
     *
     * @param   callback $func An accumulator function to be invoked on each element.
     * @param   mixed $seed
     * @throws \RuntimeException if the input sequence contains no elements.
     * @return  mixed       Returns the final result of $func.
     */
    public function aggregate($func, $seed = null)
    {
        $result = null;
        $first = true;

        if ($seed !== null) {
            $result = $seed;
            $first = false;
        }

        foreach ($this->iterator as $current) {
            if (!$first) {
                $result = $func($result, $current);
            } else {
                $result = $current;
                $first = false;
            }
        }
        if ($first) {
            throw new \RuntimeException("The input sequence contains no elements.");
        }
        return $result;
    }

    /**
     * Splits the sequence in chunks according to $chunksize.
     *
     * @param $chunksize Specifies how many elements are grouped together per chunk.
     * @throws \InvalidArgumentException
     * @return Linq
     */
    public function chunk($chunksize)
    {
        if ($chunksize < 1) {
            throw new \InvalidArgumentException("chunksize", $chunksize);
        }

        $i = -1;
        return $this->select(
            function ($x) use (&$i) {
                $i++;
                return array("index" => $i, "value" => $x);
            }
        )
        ->groupBy(
            function ($pair) use ($chunksize) {
                return $pair["index"] / $chunksize;
            }
        )
        ->select(
            function (GroupedLinq $group) {
                return $group->select(
                    function ($v) {
                        return $v["value"];
                    }
                );
            }
        );
    }

    /**
     * Determines whether all elements satisfy a condition.
     *
     * @param callback $func    A function to test each element for a condition.
     * @return bool             True if every element passes the test in the specified func, or if the sequence is empty; otherwise, false.
     */
    public function all($func)
    {
        foreach ($this->iterator as $current) {
            $match = LinqHelper::getBoolOrThrowException($func($current));
            if (!$match) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determines whether any element exists or satisfies a condition by invoking $func.
     *
     * @param callback $func    A function to test each element for a condition or NULL to determine if any element exists.
     * @return bool             True if no $func given and the source sequence contains any elements or True if any elements passed the test in the specified func; otherwise, false.
     */
    public function any($func = null)
    {
        foreach ($this->iterator as $current) {
            if ($func === null) {
                return true;
            }

            $match = LinqHelper::getBoolOrThrowException($func($current));
            if ($match) {
                return true;
            }
        }
        return false;
    }

    /**
     * Counts the elements of this Linq sequence.
     * @return int
     */
    public function count()
    {
        if ($this->iterator instanceof Countable) {
            return $this->iterator->count();
        }

        return iterator_count($this->iterator);
    }

    /**
     * Computes the average of all numeric values. Uses $func to obtain the value on each element.
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.)
     * @throws \UnexpectedValueException if an item of the sequence is not a numeric value.
     * @return double        Average of items
     */
    public function average($func = null)
    {
        $resultTotal = 0;
        $itemCount = 0;

        $source = $this->getSelectIteratorOrInnerIterator($func);

        foreach ($source as $item) {
            if (!is_numeric($item)) {
                throw new UnexpectedValueException("Cannot calculate an average on a none numeric value");
            }

            $resultTotal += $item;
            $itemCount++;
        }
        return $itemCount == 0 ? 0 : ($resultTotal / $itemCount);
    }

    /**
     * Sorts the elements in ascending order according to a key provided by $func.
     *
     * @param callback $func    A function to extract a key from an element.
     * @return Linq             A new Linq instance whose elements are sorted ascending according to a key.
     */
    public function orderBy($func)
    {
        return $this->order($func, LinqHelper::LINQ_ORDER_ASC);
    }

    /**
     * Sorts the elements in descending order according to a key provided by $func.
     *
     * @param callback $func    A function to extract a key from an element.
     * @return Linq             A new Linq instance whose elements are sorted descending according to a key.
     */
    public function orderByDescending($func)
    {
        return $this->order($func, LinqHelper::LINQ_ORDER_DESC);
    }

    private function order($func, $direction = LinqHelper::LINQ_ORDER_ASC)
    {
        return new Linq(new OrderIterator($this->iterator, $func, $direction));
    }

    /**
     * Gets the sum of all items or by invoking a transform function on each item to get a numeric value.
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.) from the given element, or NULL to use the element itself.
     * @throws \UnexpectedValueException if any element is not a numeric value.
     * @return  double         The sum of all items.
     */
    public function sum($func = null)
    {
        $sum = 0;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach ($iterator as $value) {
            if (!is_numeric($value)) {
                throw new UnexpectedValueException("sum() only works on numeric values.");
            }

            $sum += $value;
        }
        return $sum;
    }

    /**
     * Gets the minimum item value of all items or by invoking a transform function on each item to get a numeric value.
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.) from the given element, or NULL to use the element itself.
     * @throws \RuntimeException if the sequence contains no elements
     * @throws \UnexpectedValueException
     * @return  double Minimum item value
     */
    public function min($func = null)
    {
        $min = null;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach ($iterator as $value) {
            if (!is_numeric($value) && !is_string($value) && !($value instanceof \DateTime)) {
                throw new UnexpectedValueException("min() only works on numeric values, strings and DateTime objects.");
            }

            if (is_null($min)) {
                $min = $value;
            } elseif ($min > $value) {
                $min = $value;
            }
        }

        if ($min === null) {
            throw new \RuntimeException("Cannot calculate min() as the Linq sequence contains no elements.");
        }

        return $min;
    }

    /**
     * Returns the maximum item value according to $func
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.)
     * @throws \RuntimeException if the sequence contains no elements
     * @throws \UnexpectedValueException if any element is not a numeric value or a string.
     * @return double          Maximum item value
     */
    public function max($func = null)
    {
        $max = null;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach ($iterator as $value) {
            if (!is_numeric($value) && !is_string($value) && !($value instanceof \DateTime)) {
                throw new UnexpectedValueException("max() only works on numeric values, strings and DateTime objects.");
            }

            if (is_null($max)) {
                $max = $value;
            } elseif ($max < $value) {
                $max = $value;
            }
        }

        if ($max === null) {
            throw new \RuntimeException("Cannot calculate max() as the Linq sequence contains no elements.");
        }

        return $max;
    }

    /**
     * Projects each element into a new form by invoking the selector function.
     *
     * @param callback $func    A transform function to apply to each element.
     * @return Linq             A new Linq object whose elements are the result of invoking the transform function on each element of the original Linq object.
     */
    public function select($func)
    {
        return new Linq(new SelectIterator($this->iterator, $func));
    }

    /**
     * Projects each element of a sequence to a new Linq and flattens the resulting sequences into one sequence.
     *
     * @param callback $func    A func that returns a sequence (array, Linq, Iterator).
     * @throws \UnexpectedValueException if an element is not a traversable sequence.
     * @return Linq             A new Linq object whose elements are the result of invoking the one-to-many transform function on each element of the input sequence.
     */
    public function selectMany($func)
    {
        return new Linq(new SelectManyIterator(new SelectIterator($this->iterator, $func)));
    }

    /**
     * Performs the specified action on each element of the Linq sequence and returns the Linq sequence.
     * @param callback $func    A func that will be evaluated for each item in the linq sequence.
     * @return Linq             The original Linq sequence that was used to perform the foreach.
     */
    public function each($func)
    {
        foreach ($this->iterator as $item) {
            $func($item);
        }
        return $this;
    }

    /**
     * Determines whether a sequence contains a specified element.
     * This function will use php strict comparison (===). If you need custom comparison use the Linq::any($func) method.
     *
     * @param mixed     $value        The value to locate in the sequence.
     * @return bool         True if $value is found within the sequence; otherwise false.
     */
    public function contains($value)
    {
        return $this->any(
            function ($x) use ($value) {
                return $x === $value;
            }
        );
    }

    /**
     * Concatenates this Linq object with the given sequence.
     *
     * @param array|\Iterator $second A sequence which will be concatenated with this Linq object.
     * @throws InvalidArgumentException if the given sequence is not traversable.
     * @return Linq     A new Linq object that contains the concatenated elements of the input sequence and the original Linq sequence.
     */
    public function concat($second)
    {
        LinqHelper::assertArgumentIsIterable($second, "second");

        $allItems = new \ArrayIterator(array($this->iterator, $second));

        return new Linq(new SelectManyIterator($allItems));
    }

    /**
     * Returns distinct item values of this
     *
     * @param callback $func
     * @return Linq Distinct item values of this
     */
    public function distinct($func = null)
    {
        return new Linq(new DistinctIterator($this->getSelectIteratorOrInnerIterator($func)));
    }

    /**
     * Intersects the Linq sequence with second Iterable sequence.
     *
     * @param \Iterator|array An iterator to intersect with:
     * @return Linq    intersected items
     */
    public function intersect($second)
    {
        LinqHelper::assertArgumentIsIterable($second, "second");
        return new Linq(new IntersectIterator($this->iterator, LinqHelper::getIteratorOrThrow($second)));
    }

    /**
     * Returns all elements except the ones of the given sequence.
     *
     * @param array|\Iterator $second
     * @return  Linq   Returns all items of this not occuring in $second
     */
    public function except($second)
    {
        LinqHelper::assertArgumentIsIterable($second, "second");
        return new Linq(new ExceptIterator($this->iterator, LinqHelper::getIteratorOrThrow($second)));
    }

    /**
     * Returns the element at a specified index.
     * This method throws an exception if index is out of range.
     * To instead return NULL when the specified index is out of range, use the elementAtOrNull method.
     *
     * @throws \OutOfRangeException if index is less than 0 or greater than or equal to the number of elements in the sequence.
     * @param int $index
     * @return mixed Item at $index
     */
    public function elementAt($index)
    {
        return $this->getValueAt($index, true);
    }

    /**
     * Returns the element at a specified index or NULL if the index is out of range.
     *
     * @param $index
     * @return mixed Item at $index
     */
    public function elementAtOrNull($index)
    {
        return $this->getValueAt($index, false);
    }

    private function getValueAt($index, $throwEx)
    {
        $i = 0;
        foreach ($this->iterator as $value) {
            if ($i == $index) {
                return $value;
            }
            $i++;
        }

        if ($throwEx) {
            throw new OutOfRangeException("Index is less than 0 or greater than or equal to the number of elements in the sequence.");
        }

        return null;
    }

    /**
     * Groups the object according to the $func generated key
     *
     * @param callback $keySelector    a func that returns an item as key, item can be any type.
     * @return GroupedLinq
     */
    public function groupBy($keySelector)
    {
        return new Linq(new GroupIterator($this->iterator, $keySelector));
    }

    /**
     * Returns the last element that satisfies a specified condition.
     * @throws \RuntimeException if no element satisfies the condition in predicate or the source sequence is empty.
     *
     * @param callback  $func a func that returns boolean.
     * @return  Object Last item in this
     */
    public function last($func = null)
    {
        return $this->getLast($func, true);
    }

    /**
     * Returns the last element that satisfies a condition or NULL if no such element is found.
     *
     * @param callback  $func a func that returns boolean.
     * @return mixed
     */
    public function lastOrNull($func = null)
    {
        return $this->getLast($func, false);
    }

    /**
     * Returns the first element that satisfies a specified condition
     * @throws \RuntimeException if no element satisfies the condition in predicate -or- the source sequence is empty / does not match any elements.
     *
     * @param callback $func a func that returns boolean.
     * @return mixed
     */
    public function first($func = null)
    {
        return $this->getFirst($func, true);
    }

    /**
     * Returns the first element, or NULL if the sequence contains no elements.
     *
     * @param callback $func    a func that returns boolean.
     * @return mixed
     */
    public function firstOrNull($func = null)
    {
        return $this->getFirst($func, false);
    }

    /**
     * Returns the only element that satisfies a specified condition.
     *
     * @throws \RuntimeException if no element exists or if more than one element exists.
     * @param callback $func    a func that returns boolean.
     * @return mixed
     */
    public function single($func = null)
    {
        return $this->getSingle($func, true);
    }

    /**
     * Returns the only element that satisfies a specified condition or NULL if no such element exists.
     *
     * @throws \RuntimeException if more than one element satisfies the condition.
     * @param callback $func    a func that returns boolean.
     * @return mixed
     */
    public function singleOrNull($func = null)
    {
        return $this->getSingle($func, false);
    }


    private function getWhereIteratorOrInnerIterator($func)
    {
        return $func === null ? $this->iterator : new WhereIterator($this->iterator, $func);
    }

    private function getSelectIteratorOrInnerIterator($func)
    {
        return $func === null ? $this->iterator : new SelectIterator($this->iterator, $func);
    }

    private function getSingle($func, $throw)
    {
        $source = $this->getWhereIteratorOrInnerIterator($func);

        $count = 0;
        $single = null;

        foreach ($source as $stored) {
            $count++;

            if ($count > 1) {
                throw new \RuntimeException("The input sequence contains more than 1 elements.");
            }

            $single = $stored;
        }

        if ($count == 0 && $throw) {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }

        return $single;
    }

    private function getFirst($func, $throw)
    {
        $source = $this->getWhereIteratorOrInnerIterator($func);

        $count = 0;
        $first = null;

        foreach ($source as $stored) {
            $count++;
            $first = $stored;
            break;
        }

        if ($count == 0 && $throw) {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }

        return $first;
    }

    private function getLast($func, $throw)
    {
        $source = $this->getWhereIteratorOrInnerIterator($func);

        $count = 0;
        $last = null;

        foreach ($source as $stored) {
            $count++;
            $last = $stored;
        }

        if ($count == 0 && $throw) {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }

        return $last;
    }

    /**
     * Creates an Array from this Linq object with key/value selector(s).
     *
     * @param callback $keySelector     a func that returns the array-key for each element.
     * @param callback $valueSelector   a func that returns the array-value for each element.
     *
     * @return Array    An array with all values.
     */
    public function toArray($keySelector = null, $valueSelector = null)
    {
        if ($keySelector === null && $valueSelector === null) {
            return iterator_to_array($this, false);
        } elseif ($keySelector == null) {
            return iterator_to_array(new SelectIterator($this->getIterator(), $valueSelector), false);
        } else {
            $array = array();
            foreach ($this as $value) {
                $key = $keySelector($value);
                $array[$key] = $valueSelector == null ? $value : $valueSelector($value);
            }
            return $array;
        }
    }

    /**
     * Retrieves the iterator of this Linq class.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}