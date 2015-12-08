<?php

require_once("TestBase.php");

use Fusonic\Linq\Linq;

class ChainedTest extends TestBase
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
            })
            ->toArray();

        $this->assertEquals([3,4,5], $result);
    }
}