<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class GroupingTest extends TestCase
{
    public function testGroupBy()
    {
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
        $grouped = Linq::from($items)->groupBy(function ($x) {
            return $x->value;
        });

        $this->assertTrue($grouped instanceof Linq);

        $this->assertEquals(2, $grouped->count());
        $aGroup = $grouped->elementAt(0);
        $this->assertTrue($aGroup instanceof Fusonic\Linq\GroupedLinq);

        $this->assertEquals("a", $aGroup->key());
        $this->assertEquals(2, $aGroup->count());
        $this->assertSame($a1, $aGroup->elementAt(0));
        $this->assertSame($a2, $aGroup->elementAt(1));

        $bGroup = $grouped->elementAt(1);
        $this->assertEquals("b", $bGroup->key());
        $this->assertEquals(1, $bGroup->count());
        $this->assertSame($b1, $bGroup->elementAt(0));
    }
}
