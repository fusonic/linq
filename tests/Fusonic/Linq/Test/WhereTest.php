<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
{
    public function testWhere_ReturnsOnlyValuesMatching()
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
        $matching = Linq::from($items)->where(function ($v) {
            return false;
        });
        $this->assertTrue($matching instanceof Linq);

        $matching = $matching->toArray();

        $this->assertEquals(0, count($matching));

        $matching = Linq::from($items)->where(function ($v) {
            return true;
        })->toArray();
        $this->assertEquals(4, count($matching));
        $this->assertTrue(in_array($a, (array)$matching));
        $this->assertTrue(in_array($b1, (array)$matching));
        $this->assertTrue(in_array($b2, (array)$matching));
        $this->assertTrue(in_array($c, (array)$matching));

        $matching = Linq::from($items)->where(function ($v) {
            return $v->value == "b";
        })->toArray();
        $this->assertEquals(2, count($matching));
        $this->assertFalse(in_array($a, (array)$matching));
        $this->assertTrue(in_array($b1, (array)$matching));
        $this->assertTrue(in_array($b2, (array)$matching));
        $this->assertFalse(in_array($c, (array)$matching));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testWhere_ThrowsExceptionIfPredicateDoesNotReturnABoolean()
    {
        $items = ["1", "2", "3"];
        $matching = Linq::from($items)->where(function ($v) {
            return "NOT A BOOLEAN";
        });
        $matching->toArray();
    }

    public function testWhere_DoesLazyEvaluation()
    {
        $eval = false;
        $items = ["1", "2", "3"];
        $matching = Linq::from($items)->where(function ($v) use (&$eval) {
            $eval = true;
            return true;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $matching->toArray();
        $this->assertTrue($eval);
    }

    public function testWhere_EmptySequence_ReturnsEmptySequence()
    {
        $items = [];
        $matching = Linq::from($items)->where(function ($v) {
            return true;
        });

        $this->assertEquals(0, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals(0, count($array));
    }
}
