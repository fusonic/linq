<?php

require_once("TestBase.php");

use Fusonic\Linq\Linq;


class SetOperators extends TestBase
{
    public function testIntersect_ReturnsIntersectedElements()
    {
        $first = array("a", "b", "c", "d");
        $second = array("b", "c");

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("b", $array[0]);
        $this->assertEquals("c", $array[1]);
    }

    public function testIntersect_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable()
    {
        $this->assertException(function () {
            $input = array();
            $linq = Linq::from($input);
            $linq->intersect(null);
        }, self::ExceptionName_InvalidArgument);

        $this->assertException(function () {
            $input = array();
            $linq = Linq::from($input);
            $linq->intersect("Not a sequence");
        }, self::ExceptionName_InvalidArgument);
    }

    public function testIntersect_EmptySequence_ReturnsEmptySequence()
    {
        $first = array("a", "b", "c", "d");
        $second = array();

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testExcept_ReturnsAllElementsExceptTheGivenOnes()
    {
        $first = array("a", "b", "c", "d");
        $second = array("b", "c");

        $linq = Linq::from($first)->except($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("d", $array[1]);
    }

    public function testExcept_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable()
    {
        $this->assertException(function () {
            $input = array();
            $linq = Linq::from($input);
            $linq->except(null);
        }, self::ExceptionName_InvalidArgument);

        $this->assertException(function () {
            $input = array();
            $linq = Linq::from($input);
            $linq->except("Not a sequence");
        }, self::ExceptionName_InvalidArgument);
    }

    public function testExcept_EmptySequence_ReturnsAllElementsFromFirst()
    {
        $first = array("a", "b", "c", "d");
        $second = array();

        $linq = Linq::from($first)->except($second);
        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
    }

    public function testDistinct_ReturnsDistinctElements()
    {
        $items = array("a", "b", "a", "b");

        $distinct = Linq::from($items)->distinct();
        $this->assertTrue($distinct instanceof Linq);

        $this->assertEquals(2, $distinct->count());
        $distinct = $distinct->toArray();
        $this->assertEquals("a", $distinct[0]);
        $this->assertEquals("b", $distinct[1]);

        $a1 = new stdClass();
        $a1->id = 1;
        $a1->value = "a";
        $a2 = new stdClass();
        $a2->id = 2;
        $a2->value = "a";
        $b1 = new stdClass();
        $b1->id = 3;
        $b1->value = "b";

        $items = array($a1, $a2, $b1);
        $distinct = Linq::from($items)->distinct(function ($v) {
            return $v->value;
        });
        $this->assertEquals(2, $distinct->count());
    }

    public function testDistinct_DoesLazyEvaluation()
    {
        $eval = false;
        $a1 = new stdClass();
        $a1->id = 1;
        $a1->value = "a";
        $a2 = new stdClass();
        $a2->id = 2;
        $a2->value = "a";

        $items = array($a1, $a2);
        $distinct = Linq::from($items)->distinct(function ($v) use (&$eval) {
            $eval = true;
            return $v->value;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $distinct->toArray();
        $this->assertTrue($eval);
    }
}