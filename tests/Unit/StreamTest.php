<?php declare(strict_types=1);

use Borsch\Http\Stream;

test('construct without parameter', function () {
    expect($this->stream->getSize())->toBe(0)
        ->and($this->stream->tell())->toBe(0)
        ->and($this->stream->isSeekable())->toBeTrue()
        ->and($this->stream->isReadable())->toBeTrue()
        ->and($this->stream->isWritable())->toBeTrue()
        ->and($this->stream->getMetadata())->toBeArray()->not()->toBeEmpty();
});

test('write method', function () {
    $this->stream->write('Hello World');
    expect($this->stream->getSize())->toBe(11)
        ->and($this->stream->tell())->toBe(11);
    $this->stream->rewind();
    expect($this->stream->read(5))->toBe('Hello');
});

test('rewind method', function () {
    $this->stream->write('Hello World');
    $this->stream->seek(5);
    $this->stream->rewind();
    expect($this->stream->tell())->toBe(0);
});

test('seek method', function () {
    $this->stream->write('Hello World');
    $this->stream->seek(5);
    expect($this->stream->tell())->toBe(5);
});

test('eof method', function () {
    $this->stream->write('Hello World');
    $this->stream->rewind();
    expect($this->stream->eof())->toBeFalse();
    while (!$this->stream->eof()) {
        $this->stream->read(1);
    }
    expect($this->stream->eof())->toBeTrue();
});

test('read method', function () {
    $this->stream->write('Hello World');
    $this->stream->rewind();
    expect($this->stream->read(5))->toBe('Hello');
});

test('read method should return empty string when reaching the end of the stream', function () {
    $this->stream->write('Hello World');
    $this->stream->seek(11);
    expect($this->stream->read(5))->toBe('');
});

test('isWritable method should return false when the stream is in read only mode', function () {
    $stream = new Stream(mode: 'r');
    expect($stream->isWritable())->toBeFalse();
});

test('isReadable method should return false when the stream is in write only mode', function () {
    $stream = new Stream(tempnam(sys_get_temp_dir(), 'stream'), 'w');
    expect($stream->isReadable())->toBeFalse();
});

test('getContents method', function () {
    $this->stream->write('Hello World');
    $this->stream->rewind();
    expect($this->stream->getContents())->toBe('Hello World');
});

test('getContents method should return the remaining contents of the stream', function () {
    $this->stream->write('Hello World');
    $this->stream->seek(6);
    expect($this->stream->getContents())->toBe('World');
});

test('close method', function () {
    $this->stream->write('Hello World');
    $this->stream->close();
    expect($this->stream->isReadable())->toBeFalse()
        ->and($this->stream->isWritable())->toBeFalse()
        ->and($this->stream->isSeekable())->toBeFalse();
});

test('detach method', function () {
    $this->stream->write('Hello World');
    $resource = $this->stream->detach();
    expect($resource)->toBeResource()
        ->and($this->stream->getSize())->toBeNull()
        ->and($this->stream->isReadable())->toBeFalse()
        ->and($this->stream->isWritable())->toBeFalse()
        ->and($this->stream->isSeekable())->toBeFalse();
});

test('write method should throw exception when stream is not writable', function () {
    $stream = new Stream(mode: 'r');
    $stream->write('Hello World');
})->throws(RuntimeException::class);

test('read method should throw exception when stream is not readable', function () {
    $stream = new Stream(tempnam(sys_get_temp_dir(), 'stream'), 'w');
    $stream->read(5);
})->throws(RuntimeException::class);

test('seek method should throw exception when stream is not seekable', function () {
    // An HTTP stream wrapper does not support seeking.
    $stream = new Stream('https://path/to/random/stuff', 'rb');
    $stream->seek(5);
})->throws(RuntimeException::class);

test('rewind method should throw exception when stream is not seekable', function () {
    $stream = new Stream('php://output', 'r');
    $stream->rewind();
})->throws(RuntimeException::class);

test('getContents method should throw exception when stream is not readable', function () {
    $stream = new Stream(tempnam(sys_get_temp_dir(), 'stream'), 'w');
    $stream->getContents();
})->throws(RuntimeException::class);

test('getContents method should throw exception when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    $this->stream->getContents();
})->throws(RuntimeException::class);

test('getSize method should return null when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    expect($this->stream->getSize())->toBeNull();
});

test('isSeekable method should return false when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    expect($this->stream->isSeekable())->toBeFalse();
});

test('isReadable method should return false when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    expect($this->stream->isReadable())->toBeFalse();
});

test('isWritable method should return false when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    expect($this->stream->isWritable())->toBeFalse();
});

test('seek method should throw exception when stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    $this->stream->seek(5);
})->throws(RuntimeException::class);

test('rewind method should throw exception when stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    $this->stream->rewind();
})->throws(RuntimeException::class);

test('read method should throw exception when stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    $this->stream->read(5);
})->throws(RuntimeException::class);

test('write method should throw exception when resource is detached', function () {
    $this->stream->detach();
    $this->stream->write('Hello World');
})->throws(RuntimeException::class);

test('__toString method', function () {
    $this->stream->write('Hello World');
    $this->stream->rewind();
    expect((string)$this->stream)->toBe('Hello World');
});

test('__toString method should return empty string when the stream is not readable', function () {
    $stream = new Stream(tempnam(sys_get_temp_dir(), 'stream'), 'w');
    $stream->close();
    expect((string)$stream)->toBe('');
});

test('__toString method should return empty string when the stream is detached', function () {
    $this->stream->write('Hello World');
    $this->stream->detach();
    expect((string)$this->stream)->toBe('');
});
