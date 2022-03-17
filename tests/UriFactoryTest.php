<?php
/**
 * @author    Alexandre DEBUSSCHÈRE <alexandre@kosmonaft.dev>
 * @copyright 2021 Kosmonaft
 * @license   Commercial
 */

namespace BorschTest\Message;

use Borsch\Message\Uri;
use Borsch\Message\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
{

    public function testCreateUri()
    {
        $uri = (new UriFactory())->createUri('');
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('', (string)$uri);

        $uri = (new UriFactory())->createUri('https://example.com/admin/profile?foo=bar');
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('https://example.com/admin/profile?foo=bar', (string)$uri);
        $this->assertEquals('foo=bar', $uri->getQuery());
    }
}
