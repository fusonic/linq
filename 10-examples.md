---
layout: page
title: Examples
permalink: /examples/
---

## Calculate the average file size of files in a directory:

{% highlight php startinline %}
$source = glob("files/*");
Linq::from($source)
  ->select(function($i) { return filesize($i); })
  ->average();
{% endhighlight %}

## Find all files bigger than 1024 bytes and return the fileinfo object:

{% highlight php startinline %}
$source = glob("files/*");
Linq::from($source)
  ->where(function($i) { return filesize($i) > 1024; })
  ->select(function($i) { return pathinfo($i); });
{% endhighlight %}

## Search for all users containing "Max 1", Skip 5 items, Take 2 items and select the property ID of each user:

{% highlight php startinline %}
$result = Linq::from($users)
    ->where(function (User $u) { return strstr($u->surname, "Max 1");  })
    ->skip(5)
    ->take(2)
    ->select(function (User $u) { return $u->usrId; });
{% endhighlight %}

## Flatten multiple sequences into one sequence:

{% highlight php startinline %}
$array1 = array("key" => "a", "data" => array("a1", "a2"));
$array2 = array("key" => "b", "data" => array("b1", "b2"));
$array3 = array("key" => "c", "data" => array("c1", "c2"));

$allArrays = array($array1, $array2, $array3);

$result = Linq::from($allArrays)
    ->selectMany(function($x) { return $x["data"]; })
    ->toArray();
    
// $result is now: array("a1", "a2", "b1", "b2", "c1", "c2");
{% endhighlight %}

## Map sequence to array with key/value selectors:

{% highlight php startinline %}
$category1 = new stdClass(); $category1->key = 1; $category1->value = "Cars";
$category2 = new stdClass(); $category2->key = 2; $category2->value = "Ships";

$result = Linq::from(array($category1, $category2))
    ->toArray(
        function($x) { return $x->key; }, // key-selector
        function($x) { return $x->value; } // value-selector
    );
            
// $result is now: array(1 => "Cars", 2 => "Ships");
{% endhighlight %}

## The aggregate method makes it simple to perform a calculation over a sequence of values:

{% highlight php startinline %}
$numbers = Linq::from(array(1,2,3,4));
$sum = $numbers->aggregate(function($a, $b) { return $a + $b; });
// echo $sum; // output: 10 (1+2+3+4)

$chars = Linq::from(array("a", "b", "c"));
$csv = $chars->aggregate(function($a, $b) { return $a . "," . $b; });
// echo $csv; // output: "a,b,c"

$chars = Linq::from(array("a", "b", "c"));
$csv = $chars->aggregate(function($a, $b) { return $a . "," . $b; }, "seed");
// echo $csv; // output: "seed,a,b,c"
{% endhighlight %}


## The chunk method makes it simple to split a sequence into chunks of a given size:

{% highlight php startinline %}
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
{% endhighlight %}
