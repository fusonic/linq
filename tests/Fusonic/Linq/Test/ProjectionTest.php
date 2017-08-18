<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class ProjectionTest extends TestCase
{
    public function testSelect_ReturnsProjectedSequence()
    {
        $a1 = new stdClass();
        $a1->value = "a1";
        $a2 = new stdClass();
        $a2->value = "a2";
        $a3 = new stdClass();
        $a3->value = "a3";
        $a4 = new stdClass();
        $a4->value = "a4";

        // more than one
        $items = [$a1, $a2, $a3, $a4];

        $projected = Linq::from($items)->select(function ($v) {
            return $v->value;
        });

        $this->assertTrue($projected instanceof Linq);
        $this->assertEquals(4, $projected->count());

        $projected = $projected->toArray();
        $this->assertEquals("a1", $projected[0]);
        $this->assertEquals("a2", $projected[1]);
        $this->assertEquals("a3", $projected[2]);
        $this->assertEquals("a4", $projected[3]);

        $items = [];

        $projected = Linq::from($items)->select(function ($v) {
            return $v->value;
        });

        $this->assertEquals(0, $projected->count());
    }

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider selectManyFuncProvider
     */
    public function testSelectMany_throwsExceptionIfElementIsNotIterable($selectManyFunc)
    {
        $a1 = new stdClass();
        $a1->value = "a1";

        Linq::from([ $a1 ])->selectMany($selectManyFunc)->toArray();
    }

    public function selectManyFuncProvider()
    {
        return [
            [
                function ($v) {
                    return $v->value;
                }
            ],
            [
                function ($v) {
                    return null;
                }
            ]
        ];
    }

    public function testSelectMany_ReturnsFlattenedSequence()
    {
        $a1 = new stdClass();
        $a1->value = ["a", "b"];
        $a2 = new stdClass();
        $a2->value = ["c", "d"];
        $items = [$a1, $a2];

        $linq = Linq::from($items)->selectMany(function ($x) {
            return $x->value;
        });

        $this->assertTrue($linq instanceof Linq);

        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);

        // Try once again to see if rewinding the iterator works:
        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
    }

    public function testSelectMany_EmptySequence_ReturnsEmptySequence()
    {
        $linq = Linq::from([])->selectMany(function ($x) {
            return $x->value;
        });

        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));

        // Try once again to see if rewinding the iterator works:
        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testSelectMany_DoesLazyEvaluation()
    {
        $a1 = new stdClass();
        $a1->value = ["a", "b"];
        $a2 = new stdClass();
        $a2->value = ["c", "d"];
        $items = [$a1, $a2];

        $eval = false;
        $flattened = Linq::from($items)->selectMany(function ($x) use (&$eval) {
            $eval = true;
            return $x->value;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $result = $flattened->toArray();
        $this->assertTrue($eval);
    }
}
