<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class ToArrayTest extends TestCase
{
    public function testToArray_WithoutKeySelector_ReturnsIteratorValuesAsArray_UsesDefaultNumericArrayKeys()
    {
        $linq = Linq::from(["a", "b", "c"])
            ->skip(1)->take(3);

        $array = $linq->toArray();
        $this->assertTrue(is_array($array));
        $this->assertEquals(2, count($array));

        $keys = array_keys($array);
        $this->assertEquals(0, $keys[0]);
        $this->assertEquals(1, $keys[1]);

        $this->assertEquals("b", $array[0]);
        $this->assertEquals("c", $array[1]);
    }

    public function testToArray_WithKeySelector_ReturnsIteratorValuesAsArray_UsesKeySelectorValueAsKey()
    {
        $linq = Linq::from(["a", "b", "c"])
            ->skip(1)->take(3);

        $array = $linq->toArray(function ($x) {
            return "keyprefix_" . $x;
        });

        $this->assertTrue(is_array($array));
        $this->assertEquals(2, count($array));

        $keys = array_keys($array);
        $this->assertEquals("keyprefix_b", $keys[0]);
        $this->assertEquals("keyprefix_c", $keys[1]);

        $this->assertEquals("b", $array["keyprefix_b"]);
        $this->assertEquals("c", $array["keyprefix_c"]);
    }

    public function testToArray_WithKeyAndValueSelector_ReturnsArrayWithKeyValueSetsFromClosures()
    {
        $source = [
            ["catId" => 11, "name" => "Category11", "additionalcolumn" => "foo"],
            ["catId" => 12, "name" => "Category12", "additionalcolumn" => "bar"],
        ];
        $linq = Linq::from($source);

        $array = $linq->toArray(function ($x) {
            return $x["catId"];
        }, function ($y) {
            return $y["name"];
        });

        $this->assertTrue(is_array($array));
        $this->assertEquals(2, count($array));

        $keys = array_keys($array);
        $this->assertEquals(11, $keys[0]);
        $this->assertEquals(12, $keys[1]);

        $this->assertEquals("Category11", $array[11]);
        $this->assertEquals("Category12", $array[12]);
    }

    public function testToArray_WithValueSelector_ReturnsArrayWithDefaultNumericKey_AndValueFromClosure()
    {
        $source = [
            ["catId" => 11, "name" => "Category11", "additionalcolumn" => "foo"],
            ["catId" => 12, "name" => "Category12", "additionalcolumn" => "bar"],
        ];

        $linq = Linq::from($source);

        $array = $linq->toArray(null, function ($y) {
            return $y["additionalcolumn"];
        });

        $this->assertTrue(is_array($array));
        $this->assertEquals(2, count($array));

        $keys = array_keys($array);
        $this->assertEquals(0, $keys[0]);
        $this->assertEquals(1, $keys[1]);

        $this->assertEquals("foo", $array[0]);
        $this->assertEquals("bar", $array[1]);
    }

    public function testMethodsWithSequencesAsArguments_WorkWith_Arrays_Iterators_And_IteratorAggregates()
    {
        $first = Linq::from(["a", "b"]);
        $secondArray = ["c", "d"];
        $secondLinq = Linq::from(["c", "d"]);
        $secondIterator = $secondLinq->getIterator();

        $res = $first->concat($secondLinq)->toArray();
        $res = $first->intersect($secondLinq)->toArray();
        $res = $first->except($secondLinq)->toArray();

        $res = $first->concat($secondArray)->toArray();
        $res = $first->intersect($secondArray)->toArray();
        $res = $first->except($secondArray)->toArray();

        $res = $first->concat($secondIterator)->toArray();
        $res = $first->intersect($secondIterator)->toArray();
        $res = $first->except($secondIterator)->toArray();
    }
}
