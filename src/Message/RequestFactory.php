<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class RequestFactory
 */
class RequestFactory implements RequestFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (!is_string($uri) && !$uri instanceof UriInterface) {
            throw new InvalidArgumentException(sprintf(
                'Invalid URI provided, expected string or instance of UriInterface but got %s instead',
                is_object($uri) ? get_class($uri) : gettype($uri)
            ));
        }

        $uri = $uri instanceof UriInterface ? $uri : (new UriFactory())->createUri($uri);

        return (new Request())->withMethod($method)->withUri($uri);
    }
}
