<?php

require_once("TestBase.php");

use Fusonic\Linq\Linq;


class AggregatesTest extends TestBase
{
    /** Returns the only element of a sequence that satisfies a specified condition, and throws an exception if more than one such element exists.
     */
    public function testSingle_TestBehaviour()
    {
        // more than one
        $items = [1, 2];
        $this->assertException(function () use ($items) {
            Linq::from($items)->single();
        });

        // no matching elements
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->single();
        });

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->single());

        // With closure

        // more than one
        $items = [1, 2];
        $this->assertException(function () use ($items) {
            Linq::from($items)->single(function ($x) {
                return true;
            });
        });

        // no matching elements
        // because of false closure
        $this->assertException(function () use ($items) {
            Linq::from($items)->single(function ($x) {
                return false;
            });
        });

        // because of empty array
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->single(function ($x) {
                return true;
            });
        });

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->single(function ($x) {
            return true;
        }));
    }

    public function testCount_ReturnsCorrectAmounts()
    {
        $items = [1, 2];
        $this->assertEquals(2, Linq::from($items)->count());

        $items = [1, 2];
        $this->assertEquals(1, Linq::from($items)->where(function ($x) {
            return $x == 2;
        })->count());

        $items = [1, 2];
        $this->assertEquals(0, Linq::from($items)->where(function ($x) {
            return false;
        })->count());

        $items = [];
        $this->assertEquals(0, Linq::from($items)->count());
    }

    /** Returns the only element of a sequence that satisfies a specified condition or a default value if no such element exists;
     * this method throws an exception if more than one element satisfies the condition.
     */
    public function testSingleOrNull_TestBehaviour()
    {
        // more than one
        $items = [1, 2];
        $this->assertException(function () use ($items) {
            Linq::from($items)->singleOrNull();
        });

        // no matching elements
        $items = [];
        $this->assertNull(Linq::from($items)->singleOrNull());

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->singleOrNull());

        // With closure

        // more than one
        $items = [1, 2];
        $this->assertException(function () use ($items) {
            Linq::from($items)->singleOrNull(function ($x) {
                return true;
            });
        });

        // no matching elements
        // because of false closure
        $this->assertNull(Linq::from($items)->singleOrNull(function ($x) {
            return false;
        }));

        // because of empty array
        $items = [];
        $this->assertNull(Linq::from($items)->singleOrNull());

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->singleOrNull(function ($x) {
            return true;
        }));
    }

    /** Returns the first element in a sequence that satisfies a specified condition.
     *
     *  Exceptions:
     *  No element satisfies the condition in predicate.
     * -or-
     * The source sequence is empty.
     */
    public function testFirst_TestBehaviour()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        // more than one
        $items = [$a, $b1, $b2, $c];
        $this->assertSame($a, Linq::from($items)->first());

        // no matching elements
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->first();
        });

        $items = [$a];
        $this->assertSame($a, Linq::from($items)->first());

        // #### With closures ###

        // more than one
        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b1, Linq::from($items)->first(function ($x) {
            return $x->value == "b";
        }));

        // no matching elements
        // because of false closure
        $this->assertException(function () use ($items) {
            Linq::from($items)->first(function ($x) {
                return false;
            });
        });

        // because of empty array
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->first(function ($x) {
                return true;
            });
        });

        // OK
        $items = [$a];
        $this->assertSame($a, Linq::from($items)->first(function ($x) {
            return true;
        }));
    }

    /**
     * Returns the first element of a sequence, or a default value if the sequence contains no elements.
     */
    public function testFirstOrNull_DoesReturnTheFirstElement_OrNull_DoesNotThrowExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($a, Linq::from($items)->firstOrNull());

        $items = [];
        $this->assertNull(Linq::from($items)->firstOrNull());

        // #### With closures ###

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b1, Linq::from($items)->firstOrNull(function ($x) {
            return $x->value == "b";
        }));

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($c, Linq::from($items)->firstOrNull(function ($x) {
            return $x->value == "c";
        }));

        $items = [];
        $this->assertNull(Linq::from($items)->firstOrNull(function ($x) {
            return true;
        }));
    }

    public function testLast_DoesReturnTheLastElement_OrThrowsExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        // more than one
        $items = [$a, $b1, $b2, $c];
        $last = Linq::from($items)->last();
        $this->assertSame($c, $last);

        // no matching elements
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->last();
        });

        $items = [$a];
        $this->assertSame($a, Linq::from($items)->last());

        // #### With closures ###

        // more than one
        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b2, Linq::from($items)->last(function ($x) {
            return $x->value == "b";
        }));

        // no matching elements
        // because of false closure
        $this->assertException(function () use ($items) {
            Linq::from($items)->last(function ($x) {
                return false;
            });
        });

        // because of empty array
        $items = [];
        $this->assertException(function () use ($items) {
            Linq::from($items)->last(function ($x) {
                return true;
            });
        });

        // OK
        $items = [$a];
        $this->assertSame($a, Linq::from($items)->last(function ($x) {
            return true;
        }));
    }

    public function testLastOrDefault_DoesReturnTheLastElement_OrNull_DoesNotThrowExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($c, Linq::from($items)->lastOrNull());

        $items = [];
        $this->assertNull(Linq::from($items)->lastOrNull());

        // #### With closures ###

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($c, Linq::from($items)->lastOrNull(function ($x) {
            return true;
        }));

        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b2, Linq::from($items)->lastOrNull(function ($x) {
            return $x->value == "b";
        }));

        $items = [];
        $this->assertNull(Linq::from($items)->lastOrNull(function ($x) {
            return true;
        }));
    }

    public function testElementAt_ReturnsElementAtPositionOrThrowsException()
    {
        $items = ["a", "b", "c"];
        $this->assertEquals("a", Linq::from($items)->elementAt(0));
        $this->assertEquals("b", Linq::from($items)->elementAt(1));
        $this->assertEquals("c", Linq::from($items)->elementAt(2));

        $items = ["a" => "aValue", "b" => "bValue"];
        $this->assertEquals("aValue", Linq::from($items)->elementAt(0));
        $this->assertEquals("bValue", Linq::from($items)->elementAt(1));

        $this->assertException(function () {
            $items = [];
            Linq::from($items)->elementAt(0);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = [];
            Linq::from($items)->elementAt(1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = [];
            Linq::from($items)->elementAt(-1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = ["a"];
            Linq::from($items)->elementAt(1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = ["a", "b"];
            Linq::from($items)->elementAt(2);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = ["a", "b"];
            Linq::from($items)->elementAt(-1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function () {
            $items = ["a" => "value", "b" => "bValue"];
            Linq::from($items)->elementAt(2);
        }, self::ExceptionName_OutOfRange);
    }

    public function testElementAtOrNull_ReturnsElementAtPositionOrNull()
    {
        $items = ["a", "b", "c"];
        $this->assertEquals("a", Linq::from($items)->elementAtOrNull(0));
        $this->assertEquals("b", Linq::from($items)->elementAtOrNull(1));
        $this->assertEquals("c", Linq::from($items)->elementAtOrNull(2));

        $this->assertNull(Linq::from($items)->elementAtOrNull(3));
        $this->assertNull(Linq::from($items)->elementAtOrNull(4));
        $this->assertNull(Linq::from($items)->elementAtOrNull(-1));

        $items = [];
        $this->assertNull(Linq::from($items)->elementAtOrNull(3));
        $this->assertNull(Linq::from($items)->elementAtOrNull(4));
        $this->assertNull(Linq::from($items)->elementAtOrNull(-1));

        $items = ["a" => "value", "b" => "bValue"];
        $this->assertEquals("value", Linq::from($items)->elementAtOrNull(0));
        $this->assertNull(Linq::from($items)->elementAtOrNull(2));
    }

    public function testAverage_throwsExceptionIfClosureReturnsNotNumericValue()
    {
        $this->assertException(function () {
            $items = [2, new stdClass()];
            Linq::from($items)->average();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $items = [2, "no numeric value"];
            Linq::from($items)->average();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $cls = new stdClass();
            $cls->value = "no numeric value";
            $items = [$cls];
            Linq::from($items)->average(function ($x) {
                return $x->value;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testAverage_CalculatesCorrectAverage()
    {
        $items = [2, 4, 6];
        $avg = Linq::from($items)->average();
        $this->assertEquals(4, $avg);

        $items = [2.5, 2.5];
        $avg = Linq::from($items)->average();
        $this->assertEquals(2.5, $avg);

        $items = [2, "4", "6"];
        $avg = Linq::from($items)->average();
        $this->assertEquals(4, $avg);

        $items = [2, 4, 6];
        $avg = Linq::from($items)->average(function ($v) {
            return 1;
        });
        $this->assertEquals(1, $avg);

        $items = [2.5, 2.5];
        $avg = Linq::from($items)->average(function ($v) {
            return $v;
        });
        $this->assertEquals(2.5, $avg);

        $a = new stdClass();
        $a->value = 2;

        $b = new stdClass();
        $b->value = "4";

        $c = new stdClass();
        $c->value = "6";

        $items = [$a, $b, $c];
        $avg = Linq::from($items)->average(function ($v) {
            return $v->value;
        });
        $this->assertEquals(4, $avg);
    }

    public function testAll_WorksCorrectly()
    {
        // All must always return true on empty sequences:
        $items = [];
        $all = Linq::from($items)->all(function ($v) {
            return true;
        });
        $this->assertTrue($all);

        $all = Linq::from($items)->all(function ($v) {
            return false;
        });
        $this->assertTrue($all);

        // Test with values:
        $items = ["a", "b"];
        $all = Linq::from($items)->all(function ($v) {
            return $v == "a";
        });
        $this->assertFalse($all);

        $all = Linq::from($items)->all(function ($v) {
            return $v == "a" || $v == "b";
        });
        $this->assertTrue($all);
    }

    public function testAny_WithFunc_CorrectResults()
    {
        // Any must always return false on empty sequences:
        $items = [];
        $any = Linq::from($items)->any(function ($v) {
            return true;
        });
        $this->assertFalse($any);

        $any = Linq::from($items)->any(function ($v) {
            return false;
        });
        $this->assertFalse($any);

        // Test with values:
        $items = ["a", "b"];
        $any = Linq::from($items)->any(function ($v) {
            return $v == "not existing";
        });
        $this->assertFalse($any);

        $any = Linq::from($items)->any(function ($v) {
            return $v == "a";
        });
        $this->assertTrue($any);
    }

    public function testAny_WithoutFunc_CorrectResults()
    {
        $items = [];
        $this->assertFalse(Linq::from($items)->any());

        $items = ["a"];
        $this->assertTrue(Linq::from($items)->any());

        $items = ["a", "b", "c"];
        $this->assertTrue(Linq::from($items)->any());
    }

    public function testMin_ReturnsMinValueFromNumerics()
    {
        $items = [88, 77, 12, 112];
        $min = Linq::from($items)->min();
        $this->assertEquals(12, $min);

        $items = [13];
        $min = Linq::from($items)->min();
        $this->assertEquals(13, $min);

        $items = [0];
        $min = Linq::from($items)->min();
        $this->assertEquals(0, $min);

        $items = [-12];
        $min = Linq::from($items)->min();
        $this->assertEquals(-12, $min);

        $items = [-12, 0, 100, -33];
        $min = Linq::from($items)->min();
        $this->assertEquals(-33, $min);
    }

    public function testMin_ReturnsMinValueFromStrings()
    {
        $items = ["c", "a", "b", "d"];
        $min = Linq::from($items)->min();
        $this->assertEquals("a", $min);

        $items = ["a"];
        $min = Linq::from($items)->min();
        $this->assertEquals("a", $min);
    }

    public function testMin_ReturnsMinValueFromDateTimes()
    {
        $items = [
            new DateTime("2015-01-01 10:00:00"),
            new DateTime("2015-02-01 10:00:00"),
            new DateTime("2015-01-01 09:00:00"),
        ];
        $min = Linq::from($items)->min();
        $this->assertEquals($items[2], $min);
    }

    public function testMin_ThrowsExceptionIfSequenceIsEmpty()
    {
        $this->assertException(function () {
            $data = [];
            $min = Linq::from($data)->min();
        });
    }

    public function testMin_ThrowsExceptionIfSequenceContainsNoneNumericValuesOrStrings()
    {
        $this->assertException(function () {
            $data = [null];
            $max = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = [new stdClass()];
            $min = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = ["string value", 1, new stdClass()];
            $min = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $a = new stdClass();
            $a->nonNumeric = new stdClass();
            $data = [$a];
            $min = Linq::from($data)->min(function ($x) {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testMax_ReturnsMaxValueFromNumerics()
    {
        $items = [88, 77, 12, 112];
        $max = Linq::from($items)->max();
        $this->assertEquals(112, $max);

        $items = [13];
        $max = Linq::from($items)->max();
        $this->assertEquals(13, $max);

        $items = [0];
        $max = Linq::from($items)->max();
        $this->assertEquals(0, $max);

        $items = [-12];
        $max = Linq::from($items)->max();
        $this->assertEquals(-12, $max);

        $items = [-12, 0, 100, -33];
        $max = Linq::from($items)->max();
        $this->assertEquals(100, $max);
    }

    public function testMax_ReturnsMaxValueFromDateTimes()
    {
        $items = [
            new DateTime("2015-01-01 10:00:00"),
            new DateTime("2015-02-01 10:00:00"),
            new DateTime("2015-01-01 09:00:00"),
        ];
        $max = Linq::from($items)->max();
        $this->assertEquals($items[1], $max);
    }

    public function testSum_ThrowsExceptionIfSequenceContainsNoneNumericValues()
    {
        $this->assertException(function () {
            $data = [null];
            $max = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = [new stdClass()];
            $min = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = ["string value", 1, new stdClass()];
            $min = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $a = new stdClass();
            $a->value = 100;
            $a->nonNumeric = "asdf";
            $b = new stdClass();
            $b->value = 133;
            $a->nonNumeric = "asdf";

            $data = [$a, $b];
            $sum = Linq::from($data)->sum(function ($x) {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testSum_GetSumOfValues()
    {
        $data = [];
        $sum = Linq::from($data)->sum();
        $this->assertEquals(0, $sum);

        $data = [4, 9, 100.77];
        $sum = Linq::from($data)->sum();
        $this->assertEquals(113.77, $sum);

        $data = [12, -12];
        $sum = Linq::from($data)->sum();
        $this->assertEquals(0, $sum);

        $data = [12, -24];
        $sum = Linq::from($data)->sum();
        $this->assertEquals(-12, $sum);

        $a = new stdClass();
        $a->value = 100;
        $b = new stdClass();
        $b->value = 133;

        $data = [$a, $b];
        $sum = Linq::from($data)->sum(function ($x) {
            return $x->value;
        });

        $this->assertEquals(233, $sum);
    }

    public function testMax_ReturnsMaxValueFromStrings()
    {
        $items = ["c", "a", "b", "d"];
        $max = Linq::from($items)->max();
        $this->assertEquals("d", $max);

        $items = ["a"];
        $max = Linq::from($items)->max();
        $this->assertEquals("a", $max);
    }

    public function testMax_ThrowsExceptionIfSequenceIsEmpty()
    {
        $this->assertException(function () {
            $data = [];
            $max = Linq::from($data)->max();
        });
    }

    public function testMax_ThrowsExceptionIfSequenceContainsNoneNumericValuesOrStrings()
    {
        $this->assertException(function () {
            $data = [new stdClass()];
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = [null];
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $data = ["string value", 1, new stdClass()];
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function () {
            $a = new stdClass();
            $a->nonNumeric = new stdClass();
            $data = [$a];
            $min = Linq::from($data)->max(function ($x) {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testAggregate_novalues_throwsException()
    {
        $this->assertException(function () {

            Linq::from([])->aggregate(function () {
            });
        }, self::ExceptionName_Runtime);


        $this->assertException(function () {

            Linq::from([])->aggregate(function () {
            }, null);
        }, self::ExceptionName_Runtime);
    }

    public function testAggregate_returnsCorrectResult()
    {
        $this->assertEquals("value", Linq::from(["value"])->aggregate(function ($a, $b) {
            throw new Exception("Must not becalled");
        }));
        $this->assertEquals(2, Linq::from([2])->aggregate(function ($a, $b) {
            throw new Exception("Must not becalled");
        }));
        $this->assertEquals(5, Linq::from([2, 3])->aggregate(function ($a, $b) {
            return $a + $b;
        }));
        $this->assertEquals(17, Linq::from([2, 3, 3, 4, 5])->aggregate(function ($a, $b) {
            return $a + $b;
        }));
        $this->assertEquals("abcde", Linq::from(["a", "b", "c", "d", "e"])->aggregate(function ($a, $b) {
            return $a . $b;
        }));
    }

    public function testAggregate_withSeedValue_returnsCorrectResult()
    {
        $this->assertEquals(9999, Linq::from([])->aggregate(function () {
        }, 9999));
        $this->assertEquals(104, Linq::from([2])->aggregate(function ($a, $b) {
            return $a + $b;
        }, 102));
        $this->assertEquals(137, Linq::from([2, 2, 20, 11])->aggregate(function ($a, $b) {
            return $a + $b;
        }, 102));
        $this->assertEquals("begin_abcde", Linq::from(["a", "b", "c", "d", "e"])->aggregate(function ($a, $b) {
            return $a . $b;
        }, "begin_"));
    }
}