<?php

use Fusonic\Linq\Helper\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    /**
     * @test
     */
    public function add_object_addsObjectAndReturnsTrueIfObjectDoesNotExist_OtherwiseFalse()
    {
        $set = new Set();
        $a = new stdClass();
        $b = new stdClass();

        $this->assertFalse($set->contains($a));
        $this->assertTrue($set->add($a));
        $this->assertTrue($set->contains($a));
        $this->assertFalse($set->add($a));

        $this->assertFalse($set->contains($b));
        $this->assertTrue($set->add($b));
        $this->assertTrue($set->contains($b));
        $this->assertFalse($set->add($b));
        $this->assertTrue($set->contains($a));
    }

    /**
     * @test
     */
    public function add_string_addsObjectAndReturnsTrueIfObjectDoesNotExist_OtherwiseFalse()
    {
        $set = new Set();
        $a = "a";
        $b = "b";

        $this->assertFalse($set->contains($a));
        $this->assertTrue($set->add($a));
        $this->assertTrue($set->contains($a));
        $this->assertFalse($set->add($a));

        $this->assertFalse($set->contains($b));
        $this->assertTrue($set->add($b));
        $this->assertTrue($set->contains($b));
        $this->assertFalse($set->add($b));
        $this->assertTrue($set->contains($a));
    }

    /**
     * @test
     */
    public function add_array_addsObjectAndReturnsTrueIfObjectDoesNotExist_OtherwiseFalse()
    {
        $set = new Set();
        $a = ["a1", "a2"];
        $b = ["b1", "b2"];

        $this->assertFalse($set->contains($a));
        $this->assertTrue($set->add($a));
        $this->assertTrue($set->contains($a));
        $this->assertFalse($set->add($a));

        $this->assertFalse($set->contains($b));
        $this->assertTrue($set->add($b));
        $this->assertTrue($set->contains($b));
        $this->assertFalse($set->add($b));
        $this->assertTrue($set->contains($a));
    }

    /**
     * @test
     */
    public function add_null_addsObjectAndReturnsTrueIfObjectDoesNotExist_OtherwiseFalse()
    {
        $set = new Set();
        $a = null;
        $b = null;
        $c = new stdClass();

        $this->assertFalse($set->contains($a));
        $this->assertTrue($set->add($a));
        $this->assertTrue($set->contains($a));
        $this->assertFalse($set->add($a));

        $this->assertTrue($set->contains($b));
        $this->assertFalse($set->add($b));
        $this->assertTrue($set->contains($b));
        $this->assertFalse($set->add($b));
        $this->assertTrue($set->contains($a));


        $this->assertFalse($set->contains($c));
        $this->assertTrue($set->add($c));
        $this->assertTrue($set->contains($c));
        $this->assertFalse($set->add($c));
        $this->assertTrue($set->contains($c));
    }


    /**
     * @test
     */
    public function remove_object_returnTrueIfObjectExistsAndRemoveIt_OtherwiseReturnFalse()
    {
        $set = new Set();
        $a = new stdClass();
        $b = new stdClass();

        $set->add($a);
        $set->add($b);

        $this->assertTrue($set->remove($a));
        $this->assertFalse($set->remove($a));
        $this->assertFalse($set->contains($a));

        $this->assertTrue($set->contains($b));
        $this->assertTrue($set->remove($b));
        $this->assertFalse($set->remove($b));
        $this->assertFalse($set->contains($b));
    }

    /**
     * @test
     */
    public function remove_string_returnTrueIfObjectExistsAndRemoveIt_OtherwiseReturnFalse()
    {
        $set = new Set();
        $a = "a";
        $b = "b";

        $set->add($a);
        $set->add($b);

        $this->assertTrue($set->remove($a));
        $this->assertFalse($set->remove($a));
        $this->assertFalse($set->contains($a));

        $this->assertTrue($set->contains($b));
        $this->assertTrue($set->remove($b));
        $this->assertFalse($set->remove($b));
        $this->assertFalse($set->contains($b));
    }

    /**
     * @test
     */
    public function remove_array_returnTrueIfObjectExistsAndRemoveIt_OtherwiseReturnFalse()
    {
        $set = new Set();
        $a = ["a1", "a2"];
        $b = ["b1", "b2"];

        $set->add($a);
        $set->add($b);

        $this->assertTrue($set->remove($a));
        $this->assertFalse($set->remove($a));
        $this->assertFalse($set->contains($a));

        $this->assertTrue($set->contains($b));
        $this->assertTrue($set->remove($b));
        $this->assertFalse($set->remove($b));
        $this->assertFalse($set->contains($b));
    }

    /**
     * @test
     */
    public function remove_null_returnTrueIfObjectExistsAndRemoveIt_OtherwiseReturnFalse()
    {
        $set = new Set();
        $a = null;
        $b = null;

        $set->add($a);
        $set->add($b);

        $this->assertTrue($set->remove($a));
        $this->assertFalse($set->contains($a));
        $this->assertFalse($set->contains($b));

        $this->assertFalse($set->remove($a));
        $this->assertFalse($set->remove($b));

        $set->add($b);
        $this->assertTrue($set->remove($a));
        $this->assertFalse($set->contains($a));
        $this->assertFalse($set->contains($b));
    }

    /**
     * @test
     */
    public function add_remove_mixValues_CorrectBehaviour()
    {
        $set = new Set();
        $a = null;
        $b = "string";
        $c = new stdClass();
        $d = ["a", "b", "c"];

        $this->assertTrue($set->add($a));
        $this->assertTrue($set->add($b));
        $this->assertTrue($set->add($c));
        $this->assertTrue($set->add($d));

        $this->assertTrue($set->contains($a));
        $this->assertTrue($set->contains($b));
        $this->assertTrue($set->contains($c));
        $this->assertTrue($set->contains($d));

        $this->assertFalse($set->add($a));
        $this->assertFalse($set->add($b));
        $this->assertFalse($set->add($c));
        $this->assertFalse($set->add($d));

        $this->assertTrue($set->remove($a));
        $this->assertTrue($set->remove($b));
        $this->assertTrue($set->remove($c));
        $this->assertTrue($set->remove($d));

        $this->assertFalse($set->contains($a));
        $this->assertFalse($set->contains($b));
        $this->assertFalse($set->contains($c));
        $this->assertFalse($set->contains($d));

        $this->assertFalse($set->remove($a));
        $this->assertFalse($set->remove($b));
        $this->assertFalse($set->remove($c));
        $this->assertFalse($set->remove($d));
    }
}
