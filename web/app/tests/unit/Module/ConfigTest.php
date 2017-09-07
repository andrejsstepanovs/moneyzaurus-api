<?php

namespace Tests\Middleware;

use Api\Module\Config;
use Tests\TestCase;

/**
 * Class ConfigTest
 *
 * @package Tests
 */
class ConfigTest extends TestCase
{
    /** @var Config */
    private $sut;

    public function setUp()
    {
        $this->sut = new Config();
    }

    public function testGetConfigDefault()
    {
        $response = $this->sut->env('unknown', 'default_value');
        $this->assertEquals('default_value', $response);
    }

    public function testGetConfigDefaultArray()
    {
        $response = $this->sut->env(['unknown', 'unknown2'], 'default_value');
        $this->assertEquals('default_value', $response);
    }

    public function testGetConfigEnvExist()
    {
        putenv('TESTENVKEY=env_value');

        $response = $this->sut->env(['unknown', 'TESTENVKEY'], 'default_value');
        $this->assertEquals('env_value', $response);

        $response = $this->sut->env(['TESTENVKEY', 'unknown'], 'default_value');
        $this->assertEquals('env_value', $response);
    }

    public function testGetConfigNoValues()
    {
        $response = $this->sut->env(['unknown', 'unknown2']);
        $this->assertEquals(null, $response);
    }
}
