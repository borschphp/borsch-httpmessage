<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Class JsonResponse
 */
class JsonResponse extends Response
{

    /**
     * @param mixed $data
     * @param int $status_code
     * @param int $flag
     */
    public function __construct($data, int $status_code = 200, int $flag = 0)
    {
        $this->status_code = $status_code;
        $this->reason_phrase = self::$reason_phrases[$status_code] ?? '';

        if (!$data instanceof StreamInterface) {
            $json = json_encode($data, $flag);

            $data = new Stream(fopen('php://temp', 'wb+'));
            $data->write($json);
            $data->rewind();
        }

        $this->stream = $data;

        $header = 'Content-Type';
        $value = 'application/json';

        $this->header_names[$this->normalizeHeaderName($header)] = $header;
        $this->headers[$header] = $this->normalizeHeaderValue($value);
    }
}
