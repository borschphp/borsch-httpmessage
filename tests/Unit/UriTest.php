<?php declare(strict_types=1);

use Borsch\Http\Uri;

test('construct without parameter', function () {
    $uri = new Uri();

    expect($uri->getScheme())->toBe('')
        ->and($uri->getUserInfo())->toBe('')
        ->and($uri->getHost())->toBe('')
        ->and($uri->getPort())->toBeNull()
        ->and($uri->getPath())->toBe('')
        ->and($uri->getQuery())->toBe('')
        ->and($uri->getFragment())->toBe('');
});

test('should construct with full parameter', function () {
    expect($this->uri->getScheme())->toBe('https')
        ->and($this->uri->getUserInfo())->toBe('')
        ->and($this->uri->getHost())->toBe('example.com')
        ->and($this->uri->getPort())->toBe(8080)
        ->and($this->uri->getPath())->toBe('/path')
        ->and($this->uri->getQuery())->toBe('query=value')
        ->and($this->uri->getFragment())->toBe('fragment');
});

test('should return a new instance with the specified scheme', function () {
    $new_uri = $this->uri->withScheme('http');
    expect($new_uri->getScheme())->toBe('http')
        ->and($this->uri->getScheme())->toBe('https');
});

test('withScheme() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withScheme([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance with the specified user info', function () {
    $new_uri = $this->uri->withUserInfo('user:password');
    expect($new_uri->getUserInfo())->toBe('user:password')
        ->and($this->uri->getUserInfo())->toBe('');
});

test('withUserInfo() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withUserInfo([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance with the specified host', function () {
    $new_uri = $this->uri->withHost('example.org');
    expect($new_uri->getHost())->toBe('example.org')
        ->and($this->uri->getHost())->toBe('example.com');
});

test('withHost() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withHost([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance with the specified port', function () {
    $new_uri = $this->uri->withPort(80);
    expect($new_uri->getPort())->toBe(80)
        ->and($this->uri->getPort())->toBe(8080);
});

test('should return a new instance with the specified path', function () {
    $new_uri = $this->uri->withPath('/newpath');
    expect($new_uri->getPath())->toBe('/newpath')
        ->and($this->uri->getPath())->toBe('/path');
});

test('withPath() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withPath([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance with the specified query', function () {
    $new_uri = $this->uri->withQuery('newquery=newvalue');
    expect($new_uri->getQuery())->toBe('newquery=newvalue')
        ->and($this->uri->getQuery())->toBe('query=value');
});

test('withQuery() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withQuery([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance with the specified fragment', function () {
    $new_uri = $this->uri->withFragment('newfragment');
    expect($new_uri->getFragment())->toBe('newfragment')
        ->and($this->uri->getFragment())->toBe('fragment');
});

test('withFragment() should throw InvalidArgumentException when passed a non-string', function () {
    $uri = new Uri();
    $uri->withFragment([]);
})->throws(InvalidArgumentException::class);

test('should return a new instance even if the scheme, user info, host, port, path, query and fragment are the same', function () {
    $new_uri = $this->uri->withScheme('https')->withUserInfo('')->withHost('example.com')->withPort(8080)->withPath('/path')->withQuery('query=value')->withFragment('fragment');
    expect($new_uri)->not->toBe($this->uri)
        ->and($new_uri->getScheme())->toBe($this->uri->getScheme())
        ->and($new_uri->getUserInfo())->toBe($this->uri->getUserInfo())
        ->and($new_uri->getHost())->toBe($this->uri->getHost())
        ->and($new_uri->getPort())->toBe($this->uri->getPort())
        ->and($new_uri->getPath())->toBe($this->uri->getPath())
        ->and($new_uri->getQuery())->toBe($this->uri->getQuery())
        ->and($new_uri->getFragment())->toBe($this->uri->getFragment());
});

test('should return the string representation of the URI', function () {
    expect((string)$this->uri)->toBe('https://example.com:8080/path?query=value#fragment');
});

test('should return the authority of the URI', function () {
    expect($this->uri->getAuthority())->toBe('example.com:8080');
});

test('construct with invalid URI should throw InvalidArgumentException', function () {
    new Uri(':');
})->throws(InvalidArgumentException::class);

test('construct with invalid port should throw InvalidArgumentException', function () {
    new Uri('https://example.com:-80/path?query=value#fragment');
})->throws(InvalidArgumentException::class);

test('should throw exception when passing invalid port to withPort()', function () {
        $this->uri->withPort(-1);
})->throws(InvalidArgumentException::class);

test('should handle URLs that exceed the maximum allowed length', function () {
    $longUrl = str_repeat('a', 2048);
    $uri = new Uri($longUrl);
    expect($uri->getScheme())->toBe('')
        ->and($uri->getUserInfo())->toBe('')
        ->and($uri->getHost())->toBe('')
        ->and($uri->getPort())->toBe(null)
        ->and($uri->getPath())->toBe($longUrl)
        ->and($uri->getQuery())->toBe('')
        ->and($uri->getFragment())->toBe('');
});
