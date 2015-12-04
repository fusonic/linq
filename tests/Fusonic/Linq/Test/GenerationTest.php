<?php


require_once("TestBase.php");

use Fusonic\Linq\Linq;

class GenerationTest extends TestBase
{
    public function testRange_throwsExceptionIfCountIsNegative()
    {
        $this->assertException(function () {

            Linq::range(0, -1);
        }, self::ExceptionName_OutOfRange);
    }

    public function testRange_returnsRangeOfIntegers()
    {
        $range = Linq::range(0, 3)->toArray();
        $this->assertEquals(3, count($range));
        $this->assertEquals(0, $range[0]);
        $this->assertEquals(1, $range[1]);
        $this->assertEquals(2, $range[2]);

        $range = Linq::range(6, 3)->toArray();
        $this->assertEquals(3, count($range));
        $this->assertEquals(6, $range[0]);
        $this->assertEquals(7, $range[1]);
        $this->assertEquals(8, $range[2]);

        $range = Linq::range(-3, 5)->toArray();
        $this->assertEquals(5, count($range));
        $this->assertEquals(-3, $range[0]);
        $this->assertEquals(-2, $range[1]);
        $this->assertEquals(-1, $range[2]);
        $this->assertEquals(0, $range[3]);
        $this->assertEquals(1, $range[4]);
    }
}