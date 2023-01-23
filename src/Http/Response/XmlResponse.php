<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;

use Borsch\Http\Response;
use Borsch\Http\Stream;
use Psr\Http\Message\StreamInterface;
use SimpleXMLElement;

/**
 * Class XmlResponse
 */
class XmlResponse extends Response
{

    public function __construct(string|SimpleXMLElement|StreamInterface $data = '', int $status_code = 200)
    {
        if (!$data instanceof StreamInterface) {
            $xml = $data;

            if ($data instanceof SimpleXMLElement) {
                $xml = $data->asXML();
            }

            $data = new Stream('php://temp', 'wb+');
            $data->write($xml);
            $data->rewind();
        }

        parent::__construct($status_code, '', $data, [
            'Content-Type' => ['application/xml; charset=utf-8']
        ]);
    }
}
