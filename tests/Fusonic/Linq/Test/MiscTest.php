<?php
/** Copyright (C) Fusonic GmbH (http://www.fusonic.net) 2015, All rights reserved. */

require_once("TestBase.php");


use Fusonic\Linq\Linq;


class MiscTest extends TestBase
{
    public function testCountable_implementedSqlInterface()
    {
        $items = array(1, 2, 3);

        $matching = Linq::from($items);

        $this->assertEquals(3, count($matching));
        $this->assertEquals(3, $matching->count());
    }

    public function testConcat_ReturnsConcatenatedElements()
    {
        $first = array("a", "b");
        $second = array("c", "d");

        $all = Linq::from($first)->concat($second);
        $this->assertTrue($all instanceof Linq);

        $this->assertEquals(4, $all->count());

        $all = $all->toArray();
        $this->assertEquals("a", $all[0]);
        $this->assertEquals("b", $all[1]);
        $this->assertEquals("c", $all[2]);
        $this->assertEquals("d", $all[3]);
    }

    public function testConcat_ThrowsArgumentExceptionIfNoTraversableArgument()
    {
        $this->assertException(function () {
            $input = array();
            $linq = Linq::from($input);
            $linq->concat(null);
        }, self::ExceptionName_InvalidArgument);

        $this->assertException(function () {
            $input = array();
            $second = new stdClass();
            Linq::from($input)->concat($second);
        }, self::ExceptionName_InvalidArgument);
    }

    public function testLinqFrom_WorksWith_Arrays_Iterators_And_IteratorAggregates()
    {
        $linq = Linq::from(array(1, 2));
        $linq = Linq::from($linq);
        $linq = Linq::from($linq->getIterator());
    }

    public function testEach_PerformsActionOnEachElement()
    {
        $items = array("a", "b", "c");
        $looped = array();
        Linq::from($items)
            ->each(function ($x) use (&$looped) {
                $looped[] = $x;
            });

        $this->assertEquals(3, count($looped));
        $this->assertEquals("a", $looped[0]);
        $this->assertEquals("b", $looped[1]);
        $this->assertEquals("c", $looped[2]);
    }

    public function testEach_ReturnsVoid()
    {
        $linq = Linq::from(array(1, 2, 3, 4))
            ->skip(2)->take(1);

        $linqAfterEach = $linq->each(function ($x) {
        });
        $this->assertSame(null, $linqAfterEach);
    }

    public function testContains_defaultComparison()
    {
        $items = array("2", 2);
        $linq = Linq::from($items);
        $this->assertTrue($linq->contains(2));
        $this->assertTrue($linq->contains("2"));
        $this->assertFalse($linq->contains(true));

        $this->assertFalse($linq->contains(3));
        $this->assertFalse($linq->contains("3"));

        $this->assertFalse($linq->contains(3));
        $this->assertFalse($linq->contains(null));

        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();
        $linq = Linq::from(array($a, $b));
        $this->assertTrue($linq->contains($a));
        $this->assertTrue($linq->contains($b));
        $this->assertFalse($linq->contains($c));
    }
}