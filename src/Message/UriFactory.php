<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class UriFactory
 */
class UriFactory implements UriFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createUri(string $uri = ''): UriInterface
    {
        if ($uri === '') {
            return new Uri();
        }

        $uri = parse_url($uri);
        if ($uri === false) {
            throw new InvalidArgumentException('Malformed URI, unable to parse.');
        }

        return (new Uri())
            ->withScheme($uri['scheme'] ?? '')
            ->withUserInfo($uri['user'] ?? '', $uri['pass'] ?? null)
            ->withHost($uri['host'] ?? '')
            ->withPort($uri['port'] ?? null)
            ->withPath($uri['path'] ?? '')
            ->withQuery($uri['query'] ?? '')
            ->withFragment($uri['fragment'] ?? '');
    }
}
