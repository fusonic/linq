fusonic-linq 
-----------------

[![Build Status](https://travis-ci.org/fusonic/fusonic-linq.png)](https://travis-ci.org/fusonic/fusonic-linq)
[![Total Downloads](https://poser.pugx.org/fusonic/linq/downloads.png)](https://packagist.org/packages/fusonic/linq)

fusonic-linq is a PHP library inspired by the LINQ 2 Objects extension methods in .NET.

LINQ queries offer three main advantages over traditional foreach loops:

* They are more concise and readable, especially when filtering multiple conditions.

* They provide powerful filtering, ordering, and grouping capabilities with a minimum of application code.

* In general, the more complex the operation you want to perform on the data, the more benefit you will realize by using LINQ instead of traditional iteration techniques.


Requirements
------------

fusonic-linq is supported on PHP 5.3 and up.


Installation & Usage
------------

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

Once installed, include vendor/autoload.php in your script to autoload fusonic-linq.

``` php
require 'vendor/autoload.php';
use Fusonic\Linq\Linq;

Linq::from(array())->count();
```

Examples
-----

Calculate the average file size of files in a directory:
``` php
$source = glob("files/*");
Linq::from($source)
  ->select(function($i) { return filesize($i); })
  ->average();
```

Find all files bigger than 1024 bytes and return the fileinfo object:
``` php
$source = glob("files/*");
Linq::from($source)
  ->where(function($i) { return filesize($i) > 1024; })
  ->select(function($i) { return pathinfo($i); });
```

Search for all users containing "Max 1", Skip 5 items, Take 2 items and select the property ID of each user:
```php
$result = Linq::from($users)
    ->where(function (User $u) { return StringUtil::contains($u->surname, "Max 1");  })
    ->skip(5)
    ->take(2)
    ->select(function (User $u) { return $u->usrId; });
```

Flatten multiple sequences into one sequence:
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
Map sequence to array with key/value selectors:
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

The aggregate method makes it simple to perform a calculation over a sequence of values:
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

Running tests
-------------

You can run the test suite with the following command:

``` bash
phpunit --bootstrap tests/bootstrap.php .
``` 

