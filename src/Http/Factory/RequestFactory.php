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
        return new Request($method, $uri);
    }
}
