<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Borsch\Http\{Exception\InvalidArgumentException, Request, Uri};
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, UriInterface};

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
            throw InvalidArgumentException::mustBeAStringOrAnInstanceOf('Uri', UriInterface::class);
        }

        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }

        return new Request($method, $uri);
    }
}
