<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;

use Borsch\Http\Response;
use Borsch\Http\Stream;
use Psr\Http\Message\StreamInterface;
use function json_encode;

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
        if (!$data instanceof StreamInterface) {
            $json = json_encode($data, $flag);

            $data = new Stream('php://temp', 'wb+');
            $data->write($json);
            $data->rewind();
        }

        parent::__construct($status_code, '', $data, [
            'Content-Type' => ['application/json']
        ]);
    }
}
