<?php

namespace Tests\Acceptance\Transactions;

use Tests\Acceptance\TestCase;

/**
 * Class IndexTest
 *
 * @package Tests\Acceptance\Transactions
 */
class IndexTest extends TestCase
{
    public function testIndexRespondsWithExpectedJson()
    {
        $response = $this->call('GET', '/');

        $this->assertContains('json', $response->getHeader('content-type'));

        $responseData = (array)$response->json();

        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('version', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('process', $responseData);
        $this->assertEquals('V1', $responseData['version']);
    }
}