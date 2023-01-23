<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;


use Borsch\Http\Response;
use Borsch\Http\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class RedirectResponse
 */
class RedirectResponse extends Response
{

    public function __construct(string|UriInterface $uri, int $status_code = 302)
    {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        parent::__construct($status_code, '', null, [
            'Location' => [(string)$uri]
        ]);
    }
}
