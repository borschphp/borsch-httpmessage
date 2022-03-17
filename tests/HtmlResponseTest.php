<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\HtmlResponse;
use PHPUnit\Framework\TestCase;

class HtmlResponseTest extends TestCase
{

    public function test__construct()
    {
        $response = new HtmlResponse('<h1>Hello World</h1>');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals('<h1>Hello World</h1>', $response->getBody()->getContents());
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('text/html; charset=utf-8', $response->getHeaderLine('Content-Type'));
    }
}
