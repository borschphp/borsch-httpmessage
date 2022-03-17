<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

/**
 * Class EmptyResponse
 */
class EmptyResponse extends Response
{

    /**
     * @param int $status_code
     */
    public function __construct(int $status_code = 204)
    {
        $this->status_code = $status_code;
        $this->reason_phrase = self::$reason_phrases[$status_code] ?? '';
        $this->stream = new Stream(fopen('php://temp', 'r'));
    }
}
