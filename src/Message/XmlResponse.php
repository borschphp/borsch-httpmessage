<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Class XmlResponse
 */
class XmlResponse extends Response
{

    /**
     * @param StreamInterface|string $xml
     * @param int $status_code
     */
    public function __construct($xml, int $status_code = 200)
    {
        $this->status_code = $status_code;
        $this->reason_phrase = self::$reason_phrases[$status_code] ?? '';

        if (!$xml instanceof StreamInterface) {
            $data = $xml;

            $xml = new Stream(fopen('php://temp', 'wb+'));
            $xml->write($data);
            $xml->rewind();
        }

        $this->stream = $xml;

        $header = 'Content-Type';
        $value = 'application/xml; charset=utf-8';

        $this->header_names[$this->normalizeHeaderName($header)] = $header;
        $this->headers[$header] = $this->normalizeHeaderValue($value);
    }
}
