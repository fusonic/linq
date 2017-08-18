<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class SetOperators extends TestCase
{
    public function secondSequenceIsNotTraversableProvider()
    {
        return [
            [
                null
            ],
            [
                "Not a sequence"
            ]
        ];
    }

    public function testIntersect_ReturnsIntersectedElements()
    {
        $first = ["a", "b", "c", "d"];
        $second = ["b", "c"];

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("b", $array[0]);
        $this->assertEquals("c", $array[1]);

        $a1 = new stdClass();
        $a1->id = 1;
        $a1->value = "a";
        $a2 = new stdClass();
        $a2->id = 2;
        $a2->value = "a";
        $b1 = new stdClass();
        $b1->id = 3;
        $b1->value = "b";

        $items = [$a1, $a2, $b1];
        $distinct = Linq::from($items)->intersect([$a2, $b1]);

        $this->assertFalse($distinct->contains($a1));
        $this->assertTrue($distinct->contains($a2));
        $this->assertTrue($distinct->contains($b1));
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider secondSequenceIsNotTraversableProvider
     */
    public function testIntersect_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable($secondSequence)
    {
        Linq::from([ ])->intersect($secondSequence);
    }

    public function testIntersect_EmptySequence_ReturnsEmptySequence()
    {
        $first = ["a", "b", "c", "d"];
        $second = [];

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testExcept_ReturnsAllElementsExceptTheGivenOnes()
    {
        $first = ["a", "b", "c", "d"];
        $second = ["b", "c"];

        $linq = Linq::from($first)->except($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("d", $array[1]);

        $a1 = new stdClass();
        $a1->id = 1;
        $a1->value = "a";
        $a2 = new stdClass();
        $a2->id = 2;
        $a2->value = "a";
        $b1 = new stdClass();
        $b1->id = 3;
        $b1->value = "b";

        $items = [$a1, $a2, $b1];
        $distinct = Linq::from($items)->except([$a2, $b1]);

        $this->assertTrue($distinct->contains($a1));
        $this->assertFalse($distinct->contains($a2));
        $this->assertFalse($distinct->contains($b1));
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider secondSequenceIsNotTraversableProvider
     */
    public function testExcept_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable($secondSequence)
    {
        Linq::from([ ])->except($secondSequence);
    }

    public function testExcept_EmptySequence_ReturnsAllElementsFromFirst()
    {
        $first = ["a", "b", "c", "d"];
        $second = [];

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
        $items = ["a", "b", "a", "b"];

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

        $items = [$a1, $a2, $b1];
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

        $items = [$a1, $a2];
        $distinct = Linq::from($items)->distinct(function ($v) use (&$eval) {
            $eval = true;
            return $v->value;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $distinct->toArray();
        $this->assertTrue($eval);
    }
}
