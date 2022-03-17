<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Response;
use Borsch\Message\ResponseFactory;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testWithStatus()
    {
        $response = (new ResponseFactory())->createResponse(200, 'OK');
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->withStatus(201);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGetReasonPhrase()
    {
        $response = (new ResponseFactory())->createResponse(200, 'OK');
        $this->assertEquals('OK', $response->getReasonPhrase());
        $response = $response->withStatus(201, 'Created');
        $this->assertEquals('Created', $response->getReasonPhrase());
    }

    public function testGetStatusCode()
    {
        $response = (new ResponseFactory())->createResponse(200, 'OK');
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->withStatus(201);
        $this->assertEquals(201, $response->getStatusCode());
    }
}
