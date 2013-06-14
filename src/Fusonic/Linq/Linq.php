<?php

namespace Fusonic\Linq;

use Fusonic\Linq\Iterator\DiffIterator;
use Fusonic\Linq\Iterator\DistinctIterator;
use Fusonic\Linq\Iterator\GroupIterator;
use Fusonic\Linq\Iterator\IntersectIterator;
use Fusonic\Linq\Iterator\OrderIterator;
use Fusonic\Linq\Iterator\SelectIterator;
use Fusonic\Linq\Iterator\SelectManyIterator;
use Fusonic\Linq\Iterator\WhereIterator;
use Fusonic\Linq\Helper\LinqHelper;
use IteratorAggregate;
use Traversable;
use \UnexpectedValueException as UnexpectedValueException;
use \InvalidArgumentException as InvalidArgumentException;
use \OutOfRangeException as OutOfRangeException;

/**
 * LINQ 2 Objects class for PHP.
 * Provides a set of methods for querying Iterables in PHP.
 */
class Linq implements IteratorAggregate
{
    private $iterator;

    /**
     * Linq::RecursiveArrayIterator()
     *
     * @param array|Traversable $dataSource     An array or a Traversable sequence as data source
     */
    public function __construct($dataSource = array())
    {
        LinqHelper::assertIsTraversable($dataSource, "dataSource");
        $dataSource = LinqHelper::getTraversableOrThrow($dataSource);

        $this->iterator = $dataSource;
    }

    /**
     * Creates a new Linq object using the provided dataDataSource.
     * Use this method as an alternative for getting a new Linq object instance.
     *
     * @param array|Traversable $dataSource     An array or aa Traversable sequence as data source
     * @return Linq
     */
    public static function from($dataSource)
    {
        return new Linq($dataSource);
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
        if($innerIterator instanceof \ArrayIterator)
        {
            if($count >= $innerIterator->count())
            {
                return new Linq();
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
        if($count == 0)
            return new Linq();

        return new Linq(new \LimitIterator($this->iterator, 0, $count));
    }

    /**
     * Determines whether all elements satisfy a condition.
     *
     * @param callback $func    A function to test each element for a condition.
     * @return bool             True if every element passes the test in the specified func, or if the sequence is empty; otherwise, false.
     */
    public function all($func)
    {
        foreach($this->iterator as $current)
        {
            $match = LinqHelper::getBoolOrThrowException($func($current));
            if(!$match)
            {
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
        foreach($this->iterator as $current)
        {
            if($func === null)
            {
                return true;
            }

            $match = LinqHelper::getBoolOrThrowException($func($current));
            if($match)
            {
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
        if($this->iterator instanceof \Countable)
        {
            return $this->iterator->count();
        }

        return iterator_count($this->iterator);
    }

    /**
     * Computes the average of all numeric values. Uses $func to obtain the value on each element.
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.)
     * @throws \UnexpectedValueException if an item of the sequence is not a numeric value.
     * @return numeric        Average of items
     */
    public function average($func = null)
    {
        $resultTotal = 0;
        $itemCount = 0;

        $source = $this->getSelectIteratorOrInnerIterator($func);

        foreach($source as $item)
        {
            if(!is_numeric($item))
                throw new UnexpectedValueException("Cannot calculate an average on a none numeric value");

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
     * @return  numeric         The sum of all items.
     */
    public function sum($func = null)
    {
        $sum = 0;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach($iterator as $value)
        {
            if(!is_numeric($value))
                throw new UnexpectedValueException("sum() only works on numerics.");

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
     * @return  numeric Minimum item value
     */
    public function min($func = null)
    {
        $min = null;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach($iterator as $value)
        {
            if(!is_numeric($value) && !is_string($value))
                throw new UnexpectedValueException("min() only works on numeric values or strings.");

            if(is_null($min))
            {
                $min = $value;
            }
            elseif($min > $value)
            {
                $min = $value;
            }
        }

        if($min === null)
            throw new \RuntimeException("Cannot calculate min() as the Linq sequence contains no elements.");

        return $min;
    }

    /**
     * Returns the maximimum item value according to $func
     *
     * @param callback $func    A func that returns any numeric type (int, float etc.)
     * @throws \RuntimeException if the sequence contains no elements
     * @throws \UnexpectedValueException if any element is not a numeric value or a string.
     * @return numeric          Maximum item value
     */
    public function max($func = null)
    {
        $max = null;
        $iterator = $this->getSelectIteratorOrInnerIterator($func);
        foreach($iterator as $value)
        {
            if(!is_numeric($value) && !is_string($value))
                throw new UnexpectedValueException("max() only works on numeric values or strings.");

            if(is_null($max))
            {
                $max = $value;
            }
            elseif($max < $value)
            {
                $max = $value;
            }
        }

        if($max === null)
            throw new \RuntimeException("Cannot calculate min() as the Linq sequence contains no elements.");

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
     * Concatenates this Linq object with the given sequence.
     *
     * @param array|Iterator $second A sequence which will be concatenated with this Linq object.
     * @throws InvalidArgumentException if the given sequence is not traversable.
     * @return Linq     A new Linq object that contains the concatenated elements of the input sequence and the original Linq sequence.
     */
    public function concat($second)
    {
        LinqHelper::assertIsTraversable($second, "second");

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
     * @param Iterator|array An iterator to intersect with:
     * @return Linq    intersected items
     */
    public function intersect($second)
    {
        LinqHelper::assertIsTraversable($second, "second");
        return new Linq(new IntersectIterator($this->iterator, LinqHelper::getTraversableOrThrow($second)));
    }

    /**
     * Finds different items
     *
     * @param array|Iterator $second
     * @return  Linq   Returns different items of this and $array
     */
    public function diff($second)
    {
        LinqHelper::assertIsTraversable($second, "second");
        return new Linq(new DiffIterator($this->iterator, LinqHelper::getTraversableOrThrow($second)));
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
        foreach($this->iterator as $key => $value)
        {
            if($i == $index)
            {
                return $value;
            }
            $i++;
        }

        if($throwEx)
            throw new OutOfRangeException("Index is less than 0 or greater than or equal to the number of elements in the sequence.");

        return null;
    }

    /**
     * Groups the object according to the $func generated key
     *
     * @param callback $keySelector    a func that returns an item as key, item can be any type.
     * @return GroupedLinq[]
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

        foreach($source as $stored)
        {
            $count++;
            $single = $stored;
        }

        if($count == 0 && $throw)
        {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }
        else if($count > 1)
        {
            throw new \RuntimeException("The input sequence contains more than 1 elements.");
        }

        return $single;
    }

    private function getFirst($func, $throw)
    {
        $source = $this->getWhereIteratorOrInnerIterator($func);

        $count = 0;
        $first = null;

        foreach($source as $stored)
        {
            $count++;
            $first = $stored;
            break;
        }

        if($count == 0 && $throw)
        {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }

        return $first;
    }

    private function getLast($func, $throw)
    {
        $source = $this->getWhereIteratorOrInnerIterator($func);

        $count = 0;
        $last = null;

        foreach($source as $stored)
        {
            $count++;
            $last = $stored;
        }

        if($count == 0 && $throw)
        {
            throw new \RuntimeException("The input sequence contains no matching element.");
        }

        return $last;
    }

    /**
     * Creates an Array from this Linq object.
     *
     * @return Array    Linq as Array
     */
    public function toArray()
    {
        return iterator_to_array($this, false);
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