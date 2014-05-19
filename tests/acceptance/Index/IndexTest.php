<?php

namespace Tests\Acceptance\Transactions;


use Tests\Acceptance\TestCase;


class IndexTest extends TestCase
{
    public function testIndexRespondsWithExpectedJson()
    {
        $response = $this->getClient()->call('GET', '/');

        $this->assertContains('json', $response->getHeader('content-type'));

        $responseData = (array)$response->json();

        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('version', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('process', $responseData);
        $this->assertEquals('V1', $responseData['version']);
    }
}