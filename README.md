fusonic-linq [![Build Status](https://travis-ci.org/fusonic/fusonic-linq.png)](https://travis-ci.org/fusonic/fusonic-linq)
-----------------

fusonic-linq is a PHP library inspired by the LINQ extension methods in .NET.

Requirements
------------

fusonic-linq is supported on PHP 5.3 and up.


Installation & Usage
------------

The most flexible installation method is using Composer: Simply create a composer.json file in the root of your project:
``` json
{
    "require": {
        "fusonic/fusonic-linq": "@dev"
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

Calculate the average file size of files in a directory
``` php
$source = glob("files/*");
Linq::from($source)
  ->select(function($i) { return filesize($i); })
  ->average();
```

Find all files bigger than 1024 bytes and return the fileinfo object.
``` php
$source = glob("files/*");
Linq::from($source)
  ->where(function($i) { return filesize($i) > 1024; })
  ->select(function($i) { return pathinfo($i); });
```

Running tests
-------------

You can run the test suite with the following command:

``` bash
phpunit --bootstrap tests/bootstrap.php .
``` 

