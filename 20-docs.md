---
layout: page
title: Documentation
permalink: /docs/
---


## List of methods provided by fusonic/linq:

{% highlight php startinline %}
aggregate($func, $seed = null) // Applies an accumulator function over a sequence.
all($func) // Determines wheter all elements satisfy a condition.
any($func) // Determines wheter any element satisfies a condition.
average($func = null) // Computes the average of all numeric values.
concat($second) // Concatenates 2 sequences
contains($value) // Determines whether a sequence contains a specified element.
count() // Counts the elements of the sequence.
chunk($chunksize) // Splits the sequence in chunks according to $chunksize.
except($second) // Returns all items except the ones of the given sequence.
distinct($func = null) // Returns all distinct items of a sequence using the optional selector.
each($func) // Performs the specified action on each element of the sequence.
elementAt($index) // Returns the element at a specified index or throws an exception.
elementAtOrNull($index) // Returns the element at a specified index or returns null
first($func = null) // Returns the first element that satisfies a specified condition or throws an exception.
firstOrNull($func = null) // Returns the first element, or NULL if the sequence contains no elements.
groupBy($keySelector) // Groups the object according to the $keySelector generated key.
intersect($second) // Intersects the Linq sequence with second Iterable sequence.
last($func = null) // Returns the last element that satisfies a specified condition or throws an exception.
lastOrNull($func = null) // Returns the last element that satisfies a condition or NULL if no such element is found.
max($func = null) //  Returns the maximum item value according to $func.
min($func = null) //  Returns the minimum item value according to $func
orderBy($func) // Sorts the elements in ascending order according to a key provided by $func.
orderByDescending($func) // Sorts the elements in descending order according to a key provided by $func.
select($func) // Projects each element into a new form by invoking the selector function.
selectMany($func) // Projects each element of a sequence to a new Linq and flattens the resulting sequences into one sequence.
single($func = null) // Returns the only element that satisfies a specified condition or throws an exception.
singleOrDefault($func = null) // Returns the only element that satisfies a specified condition or returns Null.
skip($count) // Bypasses a specified number of elements and then returns the remaining elements.
sum($func = null) // Gets the sum of all items or by invoking a transform function on each item to get a numeric value.
take($count) // Returns a specified number of contiguous elements from the start of a sequence.
toArray($keySelector=null, $valueSelector=null) // Creates an Array from this Linq object with an optional key selector.
where($func) // Filters the Linq object according to func return result.
{% endhighlight %}

## Simple, Consistent and Predictable

One important design goal was the principle of the least surprise. As PHP is a fully dynamic language with nearly no type-safety, it is common to shoot yourself into the foot because of accidentally mixing up incompatible types.

We protect you from these programing errors by asserting that every callback functions you supply to the library must return a correctly typed value. In addition, every supported aggregate function will throw an exception if you are accidentally mixing up incompatible types.

This means that we made this library totally predictable in what it does, and verified that every function has its defined exceptions which are thrown when certain operations fail, or if certain types are not correct.

{% highlight php startinline %}
/* Throws an UnexpectedValueException if the
provided callback function does not return a boolean */
Linq::from(array("1", "1"))
->where(function($x) { return "NOT A BOOLEAN"; });

/* Throws an UnexpectedValueException if one of the values
is not convertible to a numeric value:*/
Linq::from(array(1, 2, "Not a numeric value"))
->sum();
{% endhighlight %}
