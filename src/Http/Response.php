<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 */
class Response extends Message implements ResponseInterface
{

    public function __construct(
        protected int $status_code = 200,
        protected ?string $reason_phrase = null,
        ?Stream $body = null,
        array $headers = []
    ) {
        parent::__construct('1.1', $body, $headers);
        $this->reason_phrase = $reason_phrase ?: self::getDefaultReasonPhrase($this->status_code);
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function getReasonPhrase(): string
    {
        return $this->reason_phrase;
    }

    public function withStatus(int $code, string $reason_phrase = ''): ResponseInterface
    {
        if ($this->status_code === $code && $this->reason_phrase === $reason_phrase) {
            return $this;
        }

        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException(sprintf(
                'Invalid status code "%s"; must be an integer between 100 and 599',
                $code
            ));
        }

        $new = clone $this;
        $new->status_code = $code;
        $new->reason_phrase = $reason_phrase ?: self::getDefaultReasonPhrase($code);

        return $new;
    }

    /** @infection-ignore-all */
    protected static function getDefaultReasonPhrase(int $status_code) : string
    {
        $phrases = [
            // 1xx (Informational):
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            // 2xx (Successful)
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            // 3xx (Redirection)
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            // 4xx (Client Error)
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            // 5xx (Server Error)
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        return $phrases[$status_code] ?? '';
    }
}
