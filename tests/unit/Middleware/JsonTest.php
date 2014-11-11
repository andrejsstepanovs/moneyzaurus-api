<?php

namespace Tests\Middleware;

use Api\Middleware\Json;
use Tests\TestCase;

/**
 * Class JsonTest
 *
 * @package Tests
 */
class JsonTest extends TestCase
{
    /** @var Json */
    private $sut;

    public function setUp()
    {
        $this->sut = new Json();
        $this->sut->setJsonService($this->mock()->get('\Api\Service\Json'));
        $this->sut->setApplication($this->mock()->get('\Api\Slim'));
        $this->sut->setNextMiddleware($this->mock()->get('\Slim\Middleware'));
    }

    public function testJson()
    {
        $data = array(
            'banana' => 'yellow'
        );

        $this->mock()->get('\Api\Slim')
            ->expects($this->once())
            ->method(
                'getData'
            )
            ->will($this->returnValue($data));

        $this->mock()->get('\Api\Slim')
            ->expects($this->any())
            ->method('response')
            ->will($this->returnValue($this->mock()->get('\Slim\Http\Response')));

        $this->mock()->get('\Slim\Http\Response')
            ->expects($this->any())
            ->method('headers')
            ->will($this->returnValue($this->mock()->get('\Slim\Http\Headers')));

        $this->mock()->get('\Slim\Http\Headers')
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo('Content-Type'), $this->equalTo('application/json; charset=utf-8'));

        $this->mock()->get('\Api\Service\Json')
            ->expects($this->once())
            ->method('encode')
            ->will($this->returnValue('{"banana":"yellow"}'));

        $this->mock()->get('\Slim\Http\Response')
            ->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo(json_encode($data)));

        $this->sut->call();
    }


    public function testJsonParseError()
    {
        $data = array(
            'banana' => 'yellow'
        );

        $this->mock()->get('\Api\Slim')
            ->expects($this->once())
            ->method(
                'getData'
            )
            ->will($this->returnValue($data));

        $this->mock()->get('\Api\Slim')
            ->expects($this->any())
            ->method('response')
            ->will($this->returnValue($this->mock()->get('\Slim\Http\Response')));

        $this->mock()->get('\Slim\Http\Response')
            ->expects($this->any())
            ->method('headers')
            ->will($this->returnValue($this->mock()->get('\Slim\Http\Headers')));

        $this->mock()->get('\Api\Service\Json')
            ->expects($this->once())
            ->method('encode')
            ->will($this->returnValue(false));

        $this->mock()->get('\Api\Service\Json')
            ->expects($this->once())
            ->method('getJsonErrorMessage')
            ->will($this->returnValue('TEST'));

        $this->mock()->get('\Slim\Http\Response')
            ->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('Failed to parse to json. Error: TEST'));

        $this->sut->call();
    }
}