<?php

namespace AcceptanceTests\Index;

use AcceptanceTests\TestCase;

/**
 * Class IndexTest
 *
 * @package Tests\Acceptance\Index
 */
class IndexTest extends TestCase
{
    public function testIndexRespondsWithExpectedJson()
    {
        $response = $this->get('/');

        $this->assertContains('json', strval($response->getHeader('content-type')));

        $responseData = (array) $response->json();

        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('version', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('process', $responseData);
        $this->assertEquals('V1', $responseData['version']);
    }
}
