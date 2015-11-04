<?php

namespace Fusonic\Linq\Test;

use Fusonic\Linq\Iterator\ChunkIterator;
use Fusonic\Linq\Linq;

class ChunkIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyIterator()
    {
        $x = new ChunkIterator(new \ArrayIterator([]), 1);

        $x->rewind();
        $this->assertEquals(false, $x->valid());
    }

    public function testChunkSizeLargerThanIterator()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1]), 100);

        $x->rewind();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals([0, 1], $x->current()->toArray());
    }

    public function testChunkSizeSmallerThanIterator()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2]), 2);

        $x->rewind();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals([0, 1], $x->current()->toArray());

        $x->next();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals([2], $x->current()->toArray());

        $x->next();
        $this->assertEquals(false, $x->valid());
    }

    public function testCharElementsScenario()
    {
        $x = new ChunkIterator(new \ArrayIterator(["a", "b", "c", "d", "e"]), 2);

        $x->rewind();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals(["a", "b"], $x->current()->toArray());

        $x->next();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals(["c", "d"], $x->current()->toArray());

        $x->next();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals(["e"], $x->current()->toArray());

        $x->next();
        $this->assertEquals(false, $x->valid());
    }

    public function testDifferentChunkSize()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2, 3, 4, 5, 6, 7, 8]), 3);

        $arr = array_map(
            function (Linq $y) {
                return implode(",", $y->toArray());
            },
            iterator_to_array($x)
        );
        $this->assertEquals(
            ["0,1,2", "3,4,5", "6,7,8"],
            $arr
        );
    }

    public function testIdempotency()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2]), 2);

        $x->rewind();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals([0, 1], $x->current()->toArray());
        $this->assertEquals([0, 1], $x->current()->toArray());
        $x->next();

        $this->assertEquals([2], $x->current()->toArray());
        $this->assertEquals([2], $x->current()->toArray());
    }

    public function testNext()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2]), 2);

        $x->rewind();
        $x->next();
        $this->assertEquals([2], $x->current()->toArray());
    }

    public function testKey()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2]), 2);

        $x->rewind();
        $this->assertEquals(0, $x->key());
        $x->next();
        $this->assertEquals(1, $x->key());
    }

    public function testRewind()
    {
        $x = new ChunkIterator(new \ArrayIterator([0, 1, 2]), 2);

        $x->rewind();
        $x->next();
        $x->rewind();
        $this->assertEquals(true, $x->valid());
        $this->assertEquals(0, $x->key());
        $this->assertEquals([0, 1], $x->current()->toArray());
    }
}
