<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class ChunkTest extends TestCase
{
    public function testChunkWithoutGrouping()
    {
        $log = [];
        Linq::from([0, 1, 2, 3])
            ->where(function ($x) use (&$log) {
                $log[] = 'where';
                return true;
            })
            ->select(function ($x) use (&$log) {
                $log[] = 'select';
                return $x;
            })
            ->chunk(2)
            ->each(function (Linq $chunk) use (&$log) {
                $log[] = 'each';
                return $chunk;
            });

        $this->assertEquals('where select where select each where select where select each', implode(' ', $log));
    }

    /**
     * @test
     */
    public function testChunkWithGenerator()
    {
        $gen = $this->createGenerator([0, 1, 2, 3]);
        $result = Linq::from($gen)
            ->where(function ($x) {
                return true;
            })
            ->select(function ($x) {
                return $x;
            })
            ->chunk(2)
            ->select(function (Linq $chunk) {
                return implode(",", $chunk->toArray());
            });

        $this->assertEquals('0,1|2,3', implode('|', $result->toArray()));
    }

    private function createGenerator(array $arr)
    {
        foreach ($arr as $x) {
            yield $x;
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider invalidChunkSizeProvider
     */
    public function testChunk_throwsException_IfchunksizeIsInvalid($invalidChunkSize)
    {
        Linq::from([ ])->chunk($invalidChunkSize);
    }

    public function invalidChunkSizeProvider()
    {
        return [
            [
                0
            ],
            [
                -1
            ],
            [
                null
            ],
            [
                ""
            ]
        ];
    }

    public function testChunk_ReturnsChunkedElementsAccordingToChunksize()
    {
        $groups = Linq::from([])->chunk(2);
        $this->assertEquals(0, $groups->count());

        $groups = Linq::from(["a"])->chunk(2);
        $this->assertEquals(1, $groups->count());
        $this->assertEquals(1, $groups->ElementAt(0)->count());
        $this->assertEquals("a", $groups->ElementAt(0)->ElementAt(0));

        $groups = Linq::from(["a", "b", "c", "d", "e"])->chunk(2);
        $this->assertEquals(3, $groups->count());
        $this->assertEquals(2, $groups->ElementAt(0)->count());
        $this->assertEquals("a", $groups->ElementAt(0)->ElementAt(0));
        $this->assertEquals("b", $groups->ElementAt(0)->ElementAt(1));

        $this->assertEquals(2, $groups->ElementAt(1)->count());
        $this->assertEquals("c", $groups->ElementAt(1)->ElementAt(0));
        $this->assertEquals("d", $groups->ElementAt(1)->ElementAt(1));

        $this->assertEquals(1, $groups->ElementAt(2)->count());
        $this->assertEquals("e", $groups->ElementAt(2)->ElementAt(0));

        $groups = Linq::from(["a", "b", "c", "d", "e"])->chunk(3);
        $this->assertEquals(2, $groups->count());

        $groups = Linq::from(["a", "b", "c", "d", "e"])->chunk(4);
        $this->assertEquals(2, $groups->count());

        $groups = Linq::from(["a", "b", "c", "d", "e"])->chunk(5);
        $this->assertEquals(1, $groups->count());

        $groups = Linq::from(["a", "b", "c", "d", "e"])->chunk(117);
        $this->assertEquals(1, $groups->count());
    }
}
