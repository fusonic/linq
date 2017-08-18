<?php

use Fusonic\Linq\Linq;
use PHPUnit\Framework\TestCase;

class OfTypeTest extends TestCase
{
    /**
     * @test
     */
    public function when_ofType_is_called_with_empty_array()
    {
        /** @var array $result */
        $result = Linq::from([])
            ->ofType('StubInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_array_containing_expected_interface()
    {
        /** @var Stub $expectedResult */
        $expectedResult = new Stub();

        /** @var array $result */
        $result = Linq::from([$expectedResult, new StubWithoutInterface()])
            ->ofType('StubInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $this->assertSame($expectedResult, $result[0]);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_array_containing_expected_object_type()
    {
        /** @var StubWithoutInterface $expectedResult1 */
        $expectedResult1 = new StubWithoutInterface();

        /** @var array $result */
        $result = Linq::from([new Stub(), $expectedResult1])
            ->ofType('StubWithoutInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $this->assertSame($expectedResult1, $result[0]);

        /** @var StubWithoutInterface $expectedResult2 */
        $expectedResult2 = new Stub();

        $result = Linq::from([$expectedResult2, new StubWithoutInterface()])
            ->ofType('Stub')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $this->assertSame($expectedResult2, $result[0]);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_array_not_containing_expected_interface()
    {
        /** @var array $result */
        $result = Linq::from([new StubWithoutInterface(), new StubWithoutInterface()])
            ->ofType('StubInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_array_not_containing_expected_object_type()
    {
        /** @var array $result */
        $result = Linq::from([new Stub(), new Stub()])
            ->ofType('StubWithoutInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_unknown_interface()
    {
        /** @var array $result */
        $result = Linq::from([new Stub(), new Stub()])
            ->ofType('UnknownInterface')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_unknown_object_type()
    {
        /** @var array $result */
        $result = Linq::from([new Stub(), new Stub()])
            ->ofType('UnknownObject')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_int_as_type()
    {
        /** @var int[] $expectedResult */
        $expectedResult = [1,
            2,
            10,
            20];

        $result = Linq::from([1,
            2,
            new Stub(),
            10,
            NULL,
            20])
            ->ofType('int')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_bool_as_type()
    {
        /** @var int[] $expectedResult */
        $expectedResult = [TRUE,
            FALSE];

        $result = Linq::from([0,
            'string',
            'true',
            TRUE,
            'false',
            FALSE])
            ->ofType('bool')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_string_as_type()
    {
        /** @var int[] $expectedResult */
        $expectedResult = ['string',
            'true',
            'false'];

        $result = Linq::from([0,
            'string',
            'true',
            TRUE,
            'false',
            FALSE])
            ->ofType('string')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_float_as_type()
    {
        /** @var int[] $expectedResult */
        $expectedResult = [2.5,
            10.0,
            0.3];

        $result = Linq::from([0,
            'string',
            2.5,
            10.0,
            11,
            'false',
            0.3])
            ->ofType('float')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function when_ofType_is_called_with_double_as_type()
    {
        /** @var int[] $expectedResult */
        $expectedResult = [2.5,
            10.0,
            0.3];

        $result = Linq::from([0,
            'string',
            2.5,
            10.0,
            NULL,
            11,
            'false',
            0.3])
            ->ofType('double')
            ->toArray();

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult, $result);
    }
}

/**
 * Interface for test purposes only.
 */
interface StubInterface
{}

/**
 * Class for test purposes only.
 */
final class Stub implements StubInterface
{}

/**
 * Class for test purposes only.
 */
final class StubWithoutInterface
{}
