<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        return (new ServerRequest($serverParams))->withMethod($method)->withUri($uri);
    }
}
