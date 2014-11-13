<?php

namespace Tests\Controller\Index;

use PHPUnit_Framework_TestCase;
use Api\Controller\Index\IndexController;

/**
 * Class TokenTest
 *
 * @package Tests
 */
class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var IndexController */
    private $sut;

    public function setUp()
    {
        $this->sut = new IndexController();
    }

    public function testResponseShouldBeArray()
    {
        $response = $this->sut->getResponse();

        $this->assertTrue(is_array($response));
    }

    public function testResponseShouldHaveVersionKey()
    {
        $response = $this->sut->getResponse();

        $this->assertArrayHasKey('version', $response);
        $this->assertNotEmpty($response['version']);
    }
}
