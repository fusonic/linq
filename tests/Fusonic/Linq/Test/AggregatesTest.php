<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class AggregatesTest extends TestCase
{
    public function testSingle_TestBehaviour()
    {
        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->single());

        // With closure

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->single(function ($x) {
            return true;
        }));
    }

    /**
     * @expectedException RuntimeException
     * @dataProvider singleInvalidDataProvider
     */
    public function testSingleWithInvalidData($items, $func)
    {
        Linq::from($items)->single($func);
    }

    public function singleInvalidDataProvider()
    {
        return [
            "Sequence with more than one element" => [
                [ 1, 2 ],
                null
            ],
            "No matching elements" => [
                [ ],
                null
            ],
            "Closure always returns true → multiple matches" => [
                [ 1, 2 ],
                function ($x) {
                    return true;
                }
            ],
            "Closure always returns false → no matches" => [
                [ 1, 2 ],
                function ($x) {
                    return false;
                }
            ],
            "Closure always returns true, but with empty sequence" => [
                [ ],
                function ($x) {
                    return true;
                }
            ]
        ];
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

    public function testSingleOrNull_TestBehaviour()
    {
        // no matching elements
        $items = [];
        $this->assertNull(Linq::from($items)->singleOrNull());

        // OK
        $items = [77];
        $this->assertSame(77, Linq::from($items)->singleOrNull());

        // With closure

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

    /**
     * @expectedException RuntimeException
     * @dataProvider singleOrNullInvalidDataProvider
     */
    public function testSingleOrNullWithInvalidData($items, $func)
    {
        Linq::from($items)->singleOrNull($func);
    }

    public function singleOrNullInvalidDataProvider()
    {
        return [
            "Sequence with more than one element" => [
                [ 1, 2 ],
                null
            ],
            "Closure always returns true → multiple matches" => [
                [ 1, 2 ],
                function ($x) {
                    return true;
                }
            ],
        ];
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

        $items = [$a];
        $this->assertSame($a, Linq::from($items)->first());

        // #### With closures ###

        // more than one
        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b1, Linq::from($items)->first(function ($x) {
            return $x->value == "b";
        }));

        // OK
        $items = [$a];
        $this->assertSame($a, Linq::from($items)->first(function ($x) {
            return true;
        }));
    }

    /**
     * @expectedException RuntimeException
     * @dataProvider firstInvalidDataProvider
     */
    public function testFirstWithInvalidData($items, $func)
    {
        Linq::from($items)->first($func);
    }

    public function firstInvalidDataProvider()
    {
        return [
            "No matching elements" => [
                [ ],
                null
            ],
            "Sequence is empty" => [
                [ ],
                function ($x) {
                    return true;
                }
            ],
            "Func always returns false" => [
                [ 1, 2, 3 ],
                function ($x) {
                    return false;
                }
            ]
        ];
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

        $items = [$a];
        $this->assertSame($a, Linq::from($items)->last());

        // #### With closures ###

        // more than one
        $items = [$a, $b1, $b2, $c];
        $this->assertSame($b2, Linq::from($items)->last(function ($x) {
            return $x->value == "b";
        }));

        // OK
        $items = [$a];
        $this->assertSame($a, Linq::from($items)->last(function ($x) {
            return true;
        }));
    }

    /**
     * @expectedException RuntimeException
     * @dataProvider lastInvalidDataProvider
     */
    public function testLastWithInvalidData($items, $func)
    {
        Linq::from($items)->last($func);
    }

    public function lastInvalidDataProvider()
    {
        return [
            "No matching elements" => [
                [ ],
                null
            ],
            "Sequence is empty" => [
                [ ],
                function ($x) {
                    return true;
                }
            ],
            "Func always returns false" => [
                [ 1, 2, 3 ],
                function ($x) {
                    return false;
                }
            ]
        ];
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
    }

    /**
     * @expectedException OutOfRangeException
     * @dataProvider invalidIndexProvider
     */
    public function testElementAtWithInvalidIndex($items, $index)
    {
        Linq::from($items)->elementAt($index);
    }

    public function invalidIndexProvider()
    {
        return [
            [
                [ ],
                0
            ],
            [
                [ ],
                1
            ],
            [
                [ ],
                -1
            ],
            [
                [ "a" ],
                1
            ],
            [
                [ "a", "b" ],
                2
            ],
            [
                [ "a", "b" ],
                -1
            ],
            [
                [ "a" => "value", "b" => "bValue" ],
                2
            ]
        ];
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

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider averageNonNumericValuesProvider
     */
    public function testAverage_throwsExceptionIfClosureReturnsNotNumericValue($items)
    {
        Linq::from($items)->average();
    }

    public function averageNonNumericValuesProvider()
    {
        return [
            [
                [ 2, new stdClass() ]
            ],
            [
                [ 2, "non-numeric value" ]
            ]
        ];
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testAverageWithCustomFunc_throwsExceptionIfClosureReturnsNotNumericValue()
    {
        $classWithValue = new stdClass();
        $classWithValue->value = "non-numeric value";

        Linq::from([ $classWithValue ])->average(
            function ($x) {
                return $x->value;
            }
        );
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

    /**
     * @expectedException RuntimeException
     */
    public function testMin_ThrowsExceptionIfSequenceIsEmpty()
    {
        Linq::from([ ])->min();
    }

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider minNonNumericValuesProvider
     */
    public function testMinWithNonNumericValues($items)
    {
        Linq::from($items)->min();
    }

    public function minNonNumericValuesProvider()
    {
        return [
            [
                [ new stdClass() ]
            ],
            [
                [ null ]
            ],
            [
                [ "non-numeric value", 1, new stdClass() ]
            ]
        ];
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testMinWithCustomFuncAndNonNumericValues()
    {
        $classWithValue = new stdClass();
        $classWithValue->value = new stdClass();

        Linq::from([ $classWithValue ])->min(
            function ($x) {
                return $x->value;
            }
        );
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

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider sumNonNumericValuesProvider
     */
    public function testSumWithNonNumericValues($items)
    {
        Linq::from($items)->sum();
    }

    public function sumNonNumericValuesProvider()
    {
        return [
            [
                [ new stdClass() ]
            ],
            [
                [ null ]
            ],
            [
                [ "non-numeric value", 1, new stdClass() ]
            ]
        ];
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testSumWithCustomFuncAndNonNumericValues()
    {
        $fooClassWithValue = new stdClass();
        $fooClassWithValue->value = 100;
        $fooClassWithValue->nonNumericValue = "non-numeric value";

        $barClassWithValue = new stdClass();
        $barClassWithValue->value = 200;
        $barClassWithValue->nonNumericValue = "non-numeric value";

        Linq::from([ $fooClassWithValue, $barClassWithValue ])->sum(
            function ($x) {
                return $x->nonNumericValue;
            }
        );
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

    /**
     * @expectedException RuntimeException
     */
    public function testMax_ThrowsExceptionIfSequenceIsEmpty()
    {
        Linq::from([ ])->max();
    }

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider maxNonNumericValuesProvider
     */
    public function testMaxWithNonNumericValues($items)
    {
        Linq::from($items)->max();
    }

    public function maxNonNumericValuesProvider()
    {
        return [
            [
                [ new stdClass() ]
            ],
            [
                [ null ]
            ],
            [
                [ "non-numeric value", 1, new stdClass() ]
            ]
        ];
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testMaxWithCustomFuncAndNonNumericValues()
    {
        $classWithValue = new stdClass();
        $classWithValue->value = new stdClass();

        Linq::from([ $classWithValue ])->max(
            function ($x) {
                return $x->value;
            }
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testAggregate_novalues_throwsException()
    {
        Linq::from([ ])->aggregate(
            function () {
            }
        );
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
