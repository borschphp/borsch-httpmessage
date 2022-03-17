<?php
/**
 * @author debuss-a
 */

namespace BorschTest\Message;

use Borsch\Message\Uri;
use Borsch\Message\UriFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{

    public function testWithHost()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_host = $uri->withHost('Host');
        $this->assertNotEquals($uri, $with_host);
        $this->assertEquals('host', $with_host->getHost());
    }

    public function testWithNoHostNotCloned()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_host = $uri->withHost('example.com');
        $this->assertEquals($uri, $with_host);
        $this->assertEquals($uri->getHost(), $with_host->getHost());
    }

    public function testWithQuery()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_query = $uri->withQuery('?foo=bar&baz=42');
        $this->assertNotEquals($uri, $with_query);
        $this->assertEquals('foo=bar&baz=42', $with_query->getQuery());
    }

    public function testWithNoQueryNotCloned()
    {
        $uri = (new UriFactory())->createUri('https://example.com?foo=bar&baz=42');
        $with_query = $uri->withQuery('?foo=bar&baz=42');
        $this->assertEquals($uri, $with_query);
        $this->assertEquals($uri->getQuery(), $with_query->getQuery());
    }

    public function testWithQueryThrowsExceptionIfNotString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withQuery(['foo' => 'bar', 'baz' => 42]);
    }

    public function testGetHost()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_host = $uri->withHost('instance.com');
        $this->assertNotEquals($uri, $with_host);
        $this->assertEquals('instance.com', $with_host->getHost());
    }

    public function testGetFragment()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $uri = $uri->withFragment('index');
        $this->assertEquals('index', $uri->getFragment());
    }

    public function testWithScheme()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_scheme = $uri->withScheme('http');
        $this->assertNotEquals($uri, $with_scheme);
        $this->assertEquals('http', $with_scheme->getScheme());
    }

    public function testWithSameSchemeNotCloned()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $with_scheme = $uri->withScheme('https');
        $this->assertEquals($uri, $with_scheme);
        $this->assertEquals($uri->getScheme(), $with_scheme->getScheme());
    }

    public function testWithSchemeThrowsExceptionIfNotString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withScheme([]);
    }

    public function testGetAuthority()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('example.com', $uri->getAuthority());
        $uri = $uri
            ->withUserInfo('john.doe', '123456789')
            ->withPort(6543);
        $this->assertEquals('john.doe:123456789@example.com:6543', $uri->getAuthority());
    }

    public function test__toString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('https://example.com', (string)$uri);
        $uri = $uri
            ->withScheme('http')
            ->withPort(6543)
            ->withUserInfo('jdoe', 'pass')
            ->withPath('/admin')
            ->withQuery('foo=bar')
            ->withFragment('#index');
        $this->assertEquals('http://jdoe:pass@example.com:6543/admin?foo=bar#index', (string)$uri);
    }

    public function testGetQuery()
    {
        $uri = (new UriFactory())->createUri('https://example.com?foo=bar&baz=42');
        $this->assertEquals('foo=bar&baz=42', $uri->getQuery());
        $this->assertEquals('foo=bar', $uri->withQuery('?foo=bar')->getQuery());
    }

    public function testGetScheme()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('http', $uri->withScheme('http')->getScheme());
        $this->assertEquals('sftp', $uri->withScheme('sftp')->getScheme());
    }

    public function test__CloneResetComposedUri()
    {
        $extended = new class extends Uri {
            public function getComposedUri(): ?string
            {
                return $this->composed_uri;
            }
        };

        $uri = new $extended();
        $this->assertNull($uri->getComposedUri());
        $uri = $uri->withScheme('https')->withHost('example.com');
        $this->assertEquals('https://example.com', (string)$uri);
        $this->assertEquals('https://example.com', $uri->getComposedUri());
        $uri = $uri->withScheme('http');
        $this->assertNull($uri->getComposedUri());
    }

    public function testGetPath()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('', $uri->getPath());
        $uri = $uri->withPath('/admin');
        $this->assertEquals('/admin', $uri->getPath());
    }

    public function testGetUserInfo()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('admin', $uri->withUserInfo('admin')->getUserInfo());
        $this->assertEquals('admin:pass', $uri->withUserInfo('admin', 'pass')->getUserInfo());
    }

    public function testWithPath()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('/admin', $uri->withPath('/admin')->getPath());
    }

    public function testWithPathThrowsExceptionIfNotString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withPath(['/admin']);
    }

    public function testWithUserInfo()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEquals('admin', $uri->withUserInfo('admin')->getUserInfo());
        $this->assertEquals('admin:pass', $uri->withUserInfo('admin', 'pass')->getUserInfo());
    }

    public function testWithUserInfoThrowsExceptionIfNotString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withUserInfo(['admin']);
    }

    public function testWithUserInfoPasswordThrowsExceptionIfNotString()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withUserInfo('admin', ['pass']);
    }

    public function testWithPort()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertNull($uri->getPort());
        $this->assertEquals(6543, $uri->withPort(6543)->getPort());
    }

    public function testWithPortThrowsExceptionIfNotNumeric()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withPort('borsch');
    }

    public function testWithPortThrowsExceptionIfNegative()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withPort(-42);
    }

    public function testWithPortThrowsExceptionIfOutOfRange()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $uri->withPort(65536);
    }

    public function testGetPort()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertNull($uri->getPort());
        $this->assertEquals(6543, $uri->withPort(6543)->getPort());
    }

    public function testWithFragment()
    {
        $uri = (new UriFactory())->createUri('https://example.com');
        $this->assertEmpty($uri->getFragment());
        $this->assertEquals('index', $uri->withFragment('index')->getFragment());
    }
}
