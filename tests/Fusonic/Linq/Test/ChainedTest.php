<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class ChainedTest extends TestCase
{
    public function testWhereOrderBy_returnsFilteredValuesInCorrectOrder()
    {
        $source = [1, 4, 5, 2, 3];
        $result = Linq::from($source)
            ->where(function($x) {
                return $x > 2;
            })
            ->orderBy(function($x) {
                return $x;
            });

        $this->assertEquals([3,4,5], $result->toArray());

        // Check multiple evaluations are working as well:
        $this->assertEquals([3,4,5], $result->toArray());
    }
}
