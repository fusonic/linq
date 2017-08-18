<?php

require_once(__DIR__ . "/TestIteratorAggregate.php");

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class SkipTakeTest extends TestCase
{
    public function testSkipWithTake_Combined_SkipAndTakeValuesByAmount()
    {
        $items = ["a", "b", "c", "d", "e", "f"];
        $matching = Linq::from($items)->skip(2)->take(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(0)->take(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(0)->take(2);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);

        $matching = Linq::from($items)->skip(2)->take(2);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("c", $array[0]);
        $this->assertEquals("d", $array[1]);

        $matching = Linq::from($items)->skip(4)->take(99);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("e", $array[0]);
        $this->assertEquals("f", $array[1]);
    }

    public function testTakeSkip_Combined_TakeAndSkipValuesByAmount()
    {
        $items = ["a", "b", "c", "d", "e", "f"];
        $matching = Linq::from($items)->take(0)->skip(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->take(2)->skip(2);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->take(2)->skip(0);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
    }

    public function testSkip_SkipValuesByAmount()
    {
        $items = ["a", "b", "c", "d", "e", "f"];
        $matching = Linq::from($items)->skip(2);
        $this->assertTrue($matching instanceof Linq);

        $this->assertEquals(4, $matching->count());
        $matching = $matching->toArray();

        $this->assertTrue(in_array("c", $matching));
        $this->assertTrue(in_array("d", $matching));
        $this->assertTrue(in_array("e", $matching));
        $this->assertTrue(in_array("f", $matching));

        $items = ["a", "b", "c", "d", "e", "f"];

        $matching = Linq::from($items)->skip(0);
        $this->assertEquals(6, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
        $this->assertEquals("e", $array[4]);
        $this->assertEquals("f", $array[5]);

        $matching = Linq::from($items)->skip(5);
        $this->assertEquals(1, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("f", $array[0]);

        $matching = Linq::from($items)->skip(6);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(7);
        $this->assertEquals(0, $matching->count());

        // Test against empty sequence:

        $matching = Linq::from([])->skip(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from([])->skip(6);
        $this->assertEquals(0, $matching->count());
    }

    public function testTake_TakeValuesByAmount()
    {
        $items = ["a", "b", "c", "d", "e", "f"];
        $matching = Linq::from($items)->take(4);
        $this->assertTrue($matching instanceof Linq);

        $this->assertEquals(4, $matching->count());
        $matching = $matching->toArray();
        $this->assertTrue(in_array("a", $matching));
        $this->assertTrue(in_array("b", $matching));
        $this->assertTrue(in_array("c", $matching));
        $this->assertTrue(in_array("d", $matching));

        $matching = Linq::from($items)->take(0);
        $this->assertEquals(0, $matching->count());
    }

    public function testTake_WithIteratorAggregate()
    {
        $items = new TestIteratorAggregate(["a", "b", "c", "d", "e", "f"]);
        $matching = Linq::from($items)->take(4);

        $this->assertTrue($matching instanceof Linq);
        $this->assertEquals(4, $matching->count());
    }

    public function testSkip_WithIteratorAggregate()
    {
        $items = new TestIteratorAggregate(["a", "b", "c", "d", "e", "f"]);
        $matching = Linq::from($items)->skip(2);

        $this->assertTrue($matching instanceof Linq);
        $this->assertEquals(4, $matching->count());
    }
}
