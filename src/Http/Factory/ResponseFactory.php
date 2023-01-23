<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Borsch\Http\Response;
use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface};

/**
 * Class ResponseFactory
 */
class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createResponse(int $code = 200, string $reason_phrase = ''): ResponseInterface
    {
        return new Response($code, $reason_phrase);
    }
}
