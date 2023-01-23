<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;

use Borsch\Http\Response;
use Borsch\Http\Stream;

/**
 * Class HtmlResponse
 */
class HtmlResponse extends Response
{

    public function __construct(string $html = '', int $status_code = 200)
    {
        $stream = new Stream();
        $stream->write($html);

        parent::__construct($status_code, '', $stream, [
            'Content-Type' => ['text/html; charset=utf-8']
        ]);
    }
}
