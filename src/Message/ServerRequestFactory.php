<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequestFactory
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $uri = $uri instanceof UriInterface ? $uri : (new UriFactory())->createUri($uri);
        return (new ServerRequest($serverParams))->withMethod($method)->withUri($uri);
    }
}
