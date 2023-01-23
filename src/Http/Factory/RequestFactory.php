<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Borsch\Http\Request;
use Borsch\Http\Uri;
use InvalidArgumentException;
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
            throw new InvalidArgumentException('Uri must be a string or an instance of UriInterface');
        }

        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }

        return new Request($method, $uri);
    }
}
