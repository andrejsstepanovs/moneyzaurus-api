<?php

namespace Tests\Service;

use Api\Service\AccessorTrait;
use Tests\TestCase;

/**
 * Class AccessorTraitTest
 *
 * @package Tests
 */
class AccessorTraitTest extends TestCase
{
    /** @var AccessorTrait */
    private $sut;

    public function setUp()
    {
        $this->sut = $this->getObjectForTrait('Api\Service\AccessorTrait');
    }

    public function testSetAndGetStringValue()
    {
        $value = 'value';

        $setResponse = $this->sut->setString($value);
        $getResponse = $this->sut->getString();

        $this->assertInstanceOf(get_class($this->sut), $setResponse);
        $this->assertEquals($value, $getResponse);
    }

    public function testSetAndGetObjectValue()
    {
        $value = new \stdClass();

        $setResponse = $this->sut->setObject($value);
        $getResponse = $this->sut->getObject();

        $this->assertInstanceOf(get_class($this->sut), $setResponse);
        $this->assertEquals($value, $getResponse);
    }

    public function testSetAndGetArrayValue()
    {
        $value = array('apple' => 'red');

        $setResponse = $this->sut->setArray($value);
        $getResponse = $this->sut->getArray();

        $this->assertInstanceOf(get_class($this->sut), $setResponse);
        $this->assertEquals($value, $getResponse);
    }

    public function testSetAndGetMultipleValues()
    {
        $valueOne = new \stdClass();
        $valueTwo = array('banana' => 'yellow');

        $this->sut
             ->setMyTestObjectOne($valueOne)
             ->setMyTestObjectTwo($valueTwo);

        $getResponseOne = $this->sut->getMyTestObjectOne();
        $getResponseTwo = $this->sut->getMyTestObjectTwo();

        $this->assertEquals($valueOne, $getResponseOne);
        $this->assertEquals($valueTwo, $getResponseTwo);
    }

    public function testObjectChanged()
    {
        $appleValue = 'green';
        $class = new \stdClass();

        $this->sut->setTestObject($class);
        $this->sut->getTestObject()->apple = $appleValue;

        $finalValue = $this->sut->getTestObject();

        $expected = new \stdClass();
        $expected->apple = $appleValue;

        $this->assertEquals($expected, $finalValue);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownValue()
    {
        $this->sut->getUnknownValue();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithoutProvidedValueArgument()
    {
        $this->sut->set();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCallUnknownMethod()
    {
        $this->sut->unknownMethod();
    }

    public function testSetNullValue()
    {
        $value = null;

        $this->sut->setValue($value);
        $response = $this->sut->getValue();

        $this->assertNull($response);
    }

    public function testSetZeroValue()
    {
        $value = 0;

        $this->sut->setValue($value);
        $response = $this->sut->getValue();

        $this->assertEquals($value, $response);
    }
}
