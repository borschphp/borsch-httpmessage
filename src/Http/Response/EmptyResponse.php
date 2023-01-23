<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Response;

use Borsch\Http\Response;

/**
 * Class EmptyResponse
 */
class EmptyResponse extends Response
{


    public function __construct(int $status_code = 204)
    {
        parent::__construct($status_code);
    }
}
