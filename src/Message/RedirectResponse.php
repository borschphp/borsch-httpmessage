<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\UriInterface;

/**
 * Class RedirectResponse
 */
class RedirectResponse extends Response
{

    /**
     * @param UriInterface|string $uri
     * @param int $status_code
     */
    public function __construct($uri, int $status_code = 302)
    {
        $this->status_code = $status_code;
        $this->reason_phrase = self::$reason_phrases[$status_code] ?? '';

        if (!$uri instanceof UriInterface) {
            $uri = (new UriFactory())->createUri($uri);
        }

        $header = 'Location';
        $value = (string)$uri;

        $this->header_names[$this->normalizeHeaderName($header)] = $header;
        $this->headers[$header] = $this->normalizeHeaderValue($value);
    }
}
