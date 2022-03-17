<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\EmptyResponse;
use PHPUnit\Framework\TestCase;

class EmptyResponseTest extends TestCase
{

    public function test__construct()
    {
        $response = new EmptyResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('No Content', $response->getReasonPhrase());
        $this->assertEmpty($response->getBody()->getContents());
    }
}
