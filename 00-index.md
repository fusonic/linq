---
layout: index
title: Home
permalink: /
---

> _For a full introduction read [my blog post](http://www.fusonic.net/en/blog/2013/08/14/fusonic-linq-write-less-do-more/)._
>
> LINQ queries offer three main advantages over traditional foreach loops:
>
> * They are more concise and readable, especially when filtering multiple conditions.
>
> * They provide powerful filtering, ordering, and grouping capabilities with a minimum of application code.
>
> * In general, the more complex the operation you want to perform on the data, the more benefit you will realize by using LINQ instead of traditional iteration techniques.

## Requirements

fusonic/linq is supported on PHP 5.3 and up.

## Installation & Usage

The most flexible installation method is using Composer: Simply create a composer.json file in the root of your project:
{% highlight json %}
{
    "require": {
        "fusonic/linq": "@dev"
    }
}
{% endhighlight %}

Install composer and run install command:

{% highlight bash %}
curl -s http://getcomposer.org/installer | php
php composer.phar install
{% endhighlight %}

Once installed, include vendor/autoload.php in your script to autoload fusonic/linq.

{% highlight php startinline %}
require 'vendor/autoload.php';
use Fusonic\Linq\Linq;

Linq::from(array())->count();
{% endhighlight %}

## Running tests

You can run the test suite with the following command:

{% highlight bash %}
phpunit --bootstrap tests/bootstrap.php .
{% endhighlight %}

## License

This library is licensed under the MIT license.
