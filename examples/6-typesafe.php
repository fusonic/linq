<?php
/*
 * One important design goal was the principle of the least surprise.
 * As PHP is a fully dynamic language with nearly no type-safety, it is common to shoot yourself into the foot because of accidentally mixing up incompatible types.
 * We protect you from these programing errors by asserting that every callback functions you supply to the library must return a correctly typed value.
 * In addition, every supported aggregate function will throw an exception if you are accidentally mixing up incompatible types.
 *
 * This means that we made this library totally predictable in what it does, and verified that every function has its defined exceptions
 * which are thrown when certain operations fail, or if certain types are not correct.
 *
 */
echo "<pre>";

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;

/* Throws an UnexpectedValueException if the
provided callback function does not return a boolean */
Linq::from(array("1", "1"))
    ->where(function($x) { return "NOT A BOOLEAN"; });


/* Throws an UnexpectedValueException if one of the values
is not convertible to a numeric value:*/
Linq::from(array(1, 2, "Not a numeric value"))
    ->sum();