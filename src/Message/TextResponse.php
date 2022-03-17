<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

/**
 * Class TextResponse
 */
class TextResponse extends Response
{

    /**
     * @param StreamInterface|string $text
     * @param int $status_code
     */
    public function __construct($text, int $status_code = 200)
    {
        $this->status_code = $status_code;
        $this->reason_phrase = self::$reason_phrases[$status_code] ?? '';

        if ($text instanceof StreamInterface) {
            $this->stream = $text;
        } elseif (is_string($text)) {
            $this->stream = (new StreamFactory())->createStream($text);
        } elseif (is_resource($text)) {
            $this->stream = (new StreamFactory())->createStreamFromResource($text);
        }

        $header = 'Content-Type';
        $value = 'text/plain; charset=utf-8';

        $this->header_names[$this->normalizeHeaderName($header)] = $header;
        $this->headers[$header] = $this->normalizeHeaderValue($value);
    }
}
