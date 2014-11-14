# fusonic/linq

[![Build Status](https://travis-ci.org/fusonic/linq.png)](https://travis-ci.org/fusonic/linq)
[![Total Downloads](https://poser.pugx.org/fusonic/linq/downloads.png)](https://packagist.org/packages/fusonic/linq)

fusonic/linq is a lightweight PHP library inspired by the LINQ 2 Objects extension methods in .NET.

For a full introduction read my blog-post: http://www.fusonic.net/en/blog/2013/08/14/fusonic-linq-write-less-do-more/

LINQ queries offer three main advantages over traditional foreach loops:

* They are more concise and readable, especially when filtering multiple conditions.

* They provide powerful filtering, ordering, and grouping capabilities with a minimum of application code.

* In general, the more complex the operation you want to perform on the data, the more benefit you will realize by using LINQ instead of traditional iteration techniques.

## Requirements

fusonic/linq is supported on PHP 5.3 and up.


## Installation & Usage

The most flexible installation method is using Composer: Simply create a composer.json file in the root of your project:
``` json
{
    "require": {
        "fusonic/linq": "@dev"
    }
}
```

Install composer and run install command:
``` bash
curl -s http://getcomposer.org/installer | php
php composer.phar install
``` 

Once installed, include vendor/autoload.php in your script to autoload fusonic/linq.

``` php
require 'vendor/autoload.php';
use Fusonic\Linq\Linq;

Linq::from(array())->count();
```

## Examples

### Calculate the average file size of files in a directory:
``` php
$source = glob("files/*");
Linq::from($source)
  ->select(function($i) { return filesize($i); })
  ->average();
```

### Find all files bigger than 1024 bytes and return the fileinfo object:
``` php
$source = glob("files/*");
Linq::from($source)
  ->where(function($i) { return filesize($i) > 1024; })
  ->select(function($i) { return pathinfo($i); });
```

### Search for all users containing "Max 1", Skip 5 items, Take 2 items and select the property ID of each user:
```php
$result = Linq::from($users)
    ->where(function (User $u) { return strstr($u->surname, "Max 1");  })
    ->skip(5)
    ->take(2)
    ->select(function (User $u) { return $u->usrId; });
```

### Flatten multiple sequences into one sequence:
```php
$array1 = array("key" => "a", "data" => array("a1", "a2"));
$array2 = array("key" => "b", "data" => array("b1", "b2"));
$array3 = array("key" => "c", "data" => array("c1", "c2"));

$allArrays = array($array1, $array2, $array3);

$result = Linq::from($allArrays)
    ->selectMany(function($x) { return $x["data"]; })
    ->toArray();
    
// $result is now: array("a1", "a2", "b1", "b2", "c1", "c2");

```
### Map sequence to array with key/value selectors:
```php
$category1 = new stdClass(); $category1->key = 1; $category1->value = "Cars";
$category2 = new stdClass(); $category2->key = 2; $category2->value = "Ships";

$result = Linq::from(array($category1, $category2))
    ->toArray(
        function($x) { return $x->key; }, // key-selector
        function($x) { return $x->value; } // value-selector
    );
            
// $result is now: array(1 => "Cars", 2 => "Ships");
```

### The aggregate method makes it simple to perform a calculation over a sequence of values:
```php
$numbers = Linq::from(array(1,2,3,4));
$sum = $numbers->aggregate(function($a, $b) { return $a + $b; });
// echo $sum; // output: 10 (1+2+3+4)

$chars = Linq::from(array("a", "b", "c"));
$csv = $chars->aggregate(function($a, $b) { return $a . "," . $b; });
// echo $csv; // output: "a,b,c"

$chars = Linq::from(array("a", "b", "c"));
$csv = $chars->aggregate(function($a, $b) { return $a . "," . $b; }, "seed");
// echo $csv; // output: "seed,a,b,c"

```


### The chunk method makes it simple to split a sequence into chunks of a given size:
```php
$chunks = Linq::from(array("a","b","c","d","e"))->chunk(2);
$i = 0;
foreach($chunk in $chunks) {
  $i++;
  echo "Row $i <br>";
  foreach($char in $chunk) {
    echo $char . "|";
  }
}
// Result:
// Row 1
// a|b
// Row 2
// c|d
// Row 3
// e|

```

## List of methods provided by fusonic/linq:

```php
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
```

## Simple, Consistent and Predictable

One important design goal was the principle of the least surprise. As PHP is a fully dynamic language with nearly no type-safety, it is common to shoot yourself into the foot because of accidentally mixing up incompatible types.

We protect you from these programing errors by asserting that every callback functions you supply to the library must return a correctly typed value. In addition, every supported aggregate function will throw an exception if you are accidentally mixing up incompatible types.

This means that we made this library totally predictable in what it does, and verified that every function has its defined exceptions which are thrown when certain operations fail, or if certain types are not correct.

```php
/* Throws an UnexpectedValueException if the 
provided callback function does not return a boolean */
Linq::from(array("1", "1"))
->where(function($x) { return "NOT A BOOLEAN"; });

/* Throws an UnexpectedValueException if one of the values
is not convertible to a numeric value:*/
Linq::from(array(1, 2, "Not a numeric value"))
->sum();
```

## Running tests

You can run the test suite with the following command:

```bash
phpunit --bootstrap tests/bootstrap.php .
``` 

