<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;

use Borsch\Http\Response;
use Borsch\Http\Stream;

/**
 * Class TextResponse
 */
class TextResponse extends Response
{

    public function __construct(string $text = '', int $status_code = 200)
    {
        $stream = new Stream();
        $stream->write($text);

        parent::__construct($status_code, '', $stream, [
            'Content-Type' => ['text/plain; charset=utf-8']
        ]);
    }
}
