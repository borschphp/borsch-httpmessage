<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{

    public function test__construct()
    {
        $data = ['foo' => 'bar', 'baz' => 42];
        $response = new JsonResponse($data);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals($data, json_decode($response->getBody()->getContents(), true));
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }
}
