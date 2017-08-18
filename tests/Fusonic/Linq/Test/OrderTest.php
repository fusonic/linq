<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderBy_NumericValues_WithSelector_ReturnsOrderedObjects()
    {
        $a = new stdClass();
        $a->value = 77;
        $b = new stdClass();
        $b->value = 10;
        $c = new stdClass();
        $c->value = 20;
        $items = [$a, $b, $c];

        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x->value;
        });
        $this->assertEquals(3, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($b, $items[0]);
        $this->assertSame($c, $items[1]);
        $this->assertSame($a, $items[2]);

        $a = new stdClass();
        $a->value = 77;
        $b = new stdClass();
        $b->value = 10.44;
        $c = new stdClass();
        $c->value = 20;
        $d = new stdClass();
        $d->value = 20;
        $items = [$a, $b, $c, $d];

        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x->value;
        });
        $this->assertEquals(4, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($b, $items[0]);

        // It is not predictable in which order objects with the same order key are ordered, both positions are valid:
        $pos1 = $items[1];
        $pos2 = $items[2];
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertNotsame($pos1, $pos2);

        $this->assertSame($a, $items[3]);
    }

    public function testOrderBy_NumericValues_ReturnsCorrectOrders()
    {
        $items = [77, 10, 20];
        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x;
        });
        $this->assertTrue($ascending instanceof Linq);

        $ascending = $ascending->toArray();

        $this->assertEquals(10, $ascending[0]);
        $this->assertEquals(20, $ascending[1]);
        $this->assertEquals(77, $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(77, $items[0]);
        $this->assertEquals(10, $items[1]);
        $this->assertEquals(20, $items[2]);

        $items = [12.33, 8.21, 11.3, 8.21, 33];
        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x;
        });

        $ascending = $ascending->toArray();
        $this->assertEquals(8.21, $ascending[0]);
        $this->assertEquals(8.21, $ascending[1]);
        $this->assertEquals(11.3, $ascending[2]);
        $this->assertEquals(12.33, $ascending[3]);
        $this->assertEquals(33, $ascending[4]);
    }

    public function testOrderBy_StringValues_ReturnsCorrectOrders()
    {
        $items = ["e", "a", "c"];
        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x;
        });
        $this->assertTrue($ascending instanceof Linq);

        $ascending = $ascending->toArray();
        $this->assertEquals("a", $ascending[0]);
        $this->assertEquals("c", $ascending[1]);
        $this->assertEquals("e", $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals("e", $items[0]);
        $this->assertEquals("a", $items[1]);
        $this->assertEquals("c", $items[2]);
    }

    public function testOrderBy_DateTimeValues_ReturnsCorrectOrders()
    {
        $items = [new DateTime("27.10.2011"), new DateTime("03.04.2012"), new DateTime("01.01.2005")];
        $ascending = Linq::from($items)->orderBy(function ($x) {
            return $x;
        });
        $this->assertTrue($ascending instanceof Linq);
        $ascending = $ascending->toArray();

        $this->assertEquals(new DateTime("01.01.2005"), $ascending[0]);
        $this->assertEquals(new DateTime("27.10.2011"), $ascending[1]);
        $this->assertEquals(new DateTime("03.04.2012"), $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(new DateTime("27.10.2011"), $items[0]);
        $this->assertEquals(new DateTime("03.04.2012"), $items[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $items[2]);
    }

    public function testOrderByDescending_NumericValues_ReturnsCorrectOrders()
    {
        $items = [77, 10, 20];
        $desc = Linq::from($items)->orderByDescending(function ($x) {
            return $x;
        });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals(77, $desc[0]);
        $this->assertEquals(20, $desc[1]);
        $this->assertEquals(10, $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(77, $items[0]);
        $this->assertEquals(10, $items[1]);
        $this->assertEquals(20, $items[2]);

        $items = [12.33, 8.21, 11.3, 8.21, 33];
        $desc = Linq::from($items)->orderByDescending(function ($x) {
            return $x;
        });
        $desc = $desc->toArray();
        $this->assertEquals(33, $desc[0]);
        $this->assertEquals(12.33, $desc[1]);
        $this->assertEquals(11.3, $desc[2]);
        $this->assertEquals(8.21, $desc[3]);
        $this->assertEquals(8.21, $desc[4]);
    }

    public function testOrderByDescending_NumericValues_WithSelector_ReturnsOrderedObjects()
    {
        $a = new stdClass();
        $a->value = 77;
        $b = new stdClass();
        $b->value = 10;
        $c = new stdClass();
        $c->value = 20;
        $items = [$a, $b, $c];

        $ascending = Linq::from($items)->orderByDescending(function ($x) {
            return $x->value;
        });
        $this->assertEquals(3, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($a, $items[0]);
        $this->assertSame($c, $items[1]);
        $this->assertSame($b, $items[2]);

        $a = new stdClass();
        $a->value = 77;
        $b = new stdClass();
        $b->value = 10.44;
        $c = new stdClass();
        $c->value = 20;
        $d = new stdClass();
        $d->value = 20;
        $items = [$a, $b, $c, $d];

        $ascending = Linq::from($items)->orderByDescending(function ($x) {
            return $x->value;
        });
        $this->assertEquals(4, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($a, $items[0]);
        $this->assertSame($c, $items[1]);

        // It is not predictable in which order objects with the same order key are ordered, both positions are valid:
        $pos1 = $items[2];
        $pos2 = $items[3];
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertNotsame($pos1, $pos2);
    }

    public function testOrderByDescending_DateTimeValues_ReturnsCorrectOrders()
    {
        $items = [new DateTime("27.10.2011"), new DateTime("03.04.2012"), new DateTime("01.01.2005")];
        $desc = Linq::from($items)->orderByDescending(function ($x) {
            return $x;
        });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals(new DateTime("03.04.2012"), $desc[0]);
        $this->assertEquals(new DateTime("27.10.2011"), $desc[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(new DateTime("27.10.2011"), $items[0]);
        $this->assertEquals(new DateTime("03.04.2012"), $items[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $items[2]);
    }

    public function testOrderBy_Descending_StringValues_ReturnsCorrectOrders()
    {
        $items = ["e", "a", "c"];
        $desc = Linq::from($items)->orderByDescending(function ($x) {
            return $x;
        });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals("e", $desc[0]);
        $this->assertEquals("c", $desc[1]);
        $this->assertEquals("a", $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals("e", $items[0]);
        $this->assertEquals("a", $items[1]);
        $this->assertEquals("c", $items[2]);
    }

    public function testOrderBy_DoesMakeLazyEvalution()
    {
        $items = ["e", "a", "c"];
        $eval = false;
        $linq = Linq::from($items)->orderByDescending(function ($x) use (&$eval) {
            $eval = true;
            return $x;
        });

        $this->assertFalse($eval, "OrderBy did execute before iterating!");
        $result = $linq->toArray();
        $this->assertTrue($eval);
    }

    public function testIssue3_emtpyCollectionOrdering()
    {
        Linq::from([])
            ->orderBy(function (array $x) {
                return $x["name"];
            })
            ->toArray();
    }
}
