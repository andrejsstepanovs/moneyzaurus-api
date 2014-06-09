<?php

namespace Tests\Slim;

use Api\Slim;
use Tests\TestCase;

/**
 * Class DataTest
 *
 * @package Data
 */
class SlimTest extends TestCase
{
    /** @var Slim */
    private $sut;

    public function setUp()
    {
        $this->sut = new Slim();
    }

    public function testSetData()
    {
        $data = ['apple' => 'banana'];

        $response = $this->sut->setData($data)->getData();

        $this->assertEquals($data, $response);
    }
}