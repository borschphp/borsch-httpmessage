<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\XmlResponse;
use PHPUnit\Framework\TestCase;

class XmlResponseTest extends TestCase
{

    public function test__construct()
    {
        $xml = '<root>hello world</root>';
        $response = new XmlResponse($xml);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals($xml, $response->getBody()->getContents());
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('application/xml; charset=utf-8', $response->getHeaderLine('Content-Type'));
    }
}
