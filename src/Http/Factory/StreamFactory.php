<?php declare(strict_types=1);
/**
 * @author debuss-a
 */

namespace Borsch\Http\Factory;

use Borsch\Http\Stream;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

/**
 * Class StreamFactory
 */
class StreamFactory implements StreamFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream();
        $stream->write($content);

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new Stream($filename, $mode);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        $meta = stream_get_meta_data($resource);

        return new Stream(
            $meta['uri'],
            $meta['mode']
        );
    }
}
