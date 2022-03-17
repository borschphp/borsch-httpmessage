<?php
/**
 * @author    Alexandre DEBUSSCHÈRE <alexandre@kosmonaft.dev>
 * @copyright 2021 Kosmonaft
 * @license   Commercial
 */

namespace BorschTest\Message;

use Borsch\Message\TextResponse;
use PHPUnit\Framework\TestCase;

class TextResponseTest extends TestCase
{

    public function test__construct()
    {
        $response = new TextResponse('Hello World');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals('Hello World', $response->getBody()->getContents());
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('text/plain; charset=utf-8', $response->getHeaderLine('Content-Type'));
    }
}
