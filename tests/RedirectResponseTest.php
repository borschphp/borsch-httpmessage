<?php
/**
 * @author    Alexandre DEBUSSCHÈRE <alexandre@kosmonaft.dev>
 * @copyright 2021 Kosmonaft
 * @license   Commercial
 */

namespace BorschTest\Message;

use Borsch\Message\RedirectResponse;
use PHPUnit\Framework\TestCase;

class RedirectResponseTest extends TestCase
{

    public function test__construct()
    {
        $response = new RedirectResponse('https://example.com');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Found', $response->getReasonPhrase());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals('https://example.com', $response->getHeaderLine('Location'));
    }
}
