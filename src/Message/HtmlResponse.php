<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

/**
 * Class HtmlResponse
 */
class HtmlResponse extends TextResponse
{

    public function __construct($text, int $status_code = 200)
    {
        parent::__construct($text, $status_code);

        $this->headers['Content-Type'] = $this->normalizeHeaderValue('text/html; charset=utf-8');
    }
}
