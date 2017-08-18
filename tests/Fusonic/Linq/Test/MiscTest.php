<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class MiscTest extends TestCase
{
    public function testCountable_implementedSqlInterface()
    {
        $items = [1, 2, 3];

        $matching = Linq::from($items);

        $this->assertEquals(3, count($matching));
        $this->assertEquals(3, $matching->count());
    }

    public function testConcat_ReturnsConcatenatedElements()
    {
        $first = ["a", "b"];
        $second = ["c", "d"];

        $all = Linq::from($first)->concat($second);
        $this->assertTrue($all instanceof Linq);

        $this->assertEquals(4, $all->count());

        $all = $all->toArray();
        $this->assertEquals("a", $all[0]);
        $this->assertEquals("b", $all[1]);
        $this->assertEquals("c", $all[2]);
        $this->assertEquals("d", $all[3]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider concatSecondSequenceProvider
     */
    public function testConcat_ThrowsArgumentExceptionIfNoTraversableArgument($secondSequence)
    {
        Linq::from([ ])->concat($secondSequence);
    }

    public function concatSecondSequenceProvider()
    {
        return [
            [
                null
            ],
            [
                new stdClass()
            ]
        ];
    }

    public function testLinqFrom_WorksWith_Arrays_Iterators_And_IteratorAggregates()
    {
        $linq = Linq::from([1, 2]);
        $linq = Linq::from($linq);
        $linq = Linq::from($linq->getIterator());
    }

    public function testEach_PerformsActionOnEachElement()
    {
        $items = ["a", "b", "c"];
        $looped = [];
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
        $linq = Linq::from([1, 2, 3, 4])
            ->skip(2)->take(1);

        $linqAfterEach = $linq->each(function ($x) {
        });
        $this->assertSame(null, $linqAfterEach);
    }

    public function testContains_defaultComparison()
    {
        $items = ["2", 2];
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
        $linq = Linq::from([$a, $b]);
        $this->assertTrue($linq->contains($a));
        $this->assertTrue($linq->contains($b));
        $this->assertFalse($linq->contains($c));
    }
}
