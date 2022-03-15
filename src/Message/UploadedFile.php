<?php
/**
 * @author debuss-a
 */

namespace Borsch\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFile
 */
class UploadedFile implements UploadedFileInterface
{

    /**
     * @const array
     * @link https://www.php.net/manual/en/features.file-upload.errors.php
     */
    private const ERRORS = [
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
    ];

    /** @var StreamInterface */
    protected $stream;

    /** @var string */
    protected $file;

    /** @var int */
    protected $size;

    /** @var int */
    protected $error;

    /** @var string */
    protected $client_filename;

    /** @var string */
    protected $client_media_type;

    /** @var bool */
    protected $moved = false;

    /**
     * @param StreamInterface $stream
     * @param int $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(StreamInterface $stream, int $size, int $error = UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null)
    {
        if (!isset(self::ERRORS[$error])) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not valid error status for "UploadedFile". It must be one of "UPLOAD_ERR_*" constants:  "%s".',
                $error,
                implode('", "', array_keys(self::ERRORS))
            ));
        }

        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->client_filename = $clientFilename;
        $this->client_media_type = $clientMediaType;

        if ($error === UPLOAD_ERR_OK) {
            $this->stream = $stream;
        }
    }


    /**
     * @inheritDoc
     */
    public function getStream()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(sprintf(
                'An error occured with upload. Error code %s.',
                $this->error
            ));
        }

        if ($this->moved) {
            throw new RuntimeException('The stream is not available because it has been moved.');
        }

        if ($this->stream === null) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function moveTo($targetPath)
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(sprintf(
                'An error occured with upload. Error code %s.',
                $this->error
            ));
        }

        if ($this->moved) {
            throw new RuntimeException('The file cannot be moved because it has already been moved.');
        }

        if (!is_string($targetPath)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid target path for move. Expected string but got %s.',
                is_object($targetPath) ? get_class($targetPath) : gettype($targetPath)
            ));
        }

        if (!strlen($targetPath)) {
            throw new InvalidArgumentException('Target path is not valid for move, non-empty string required.');
        }

        $directory = dirname($targetPath);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new RuntimeException(sprintf(
                'The target directory "%s" does not exist or is not writable.',
                $directory
            ));
        }

        $file = fopen($targetPath, 'wb+');
        if (!$file) {
            throw new RuntimeException(sprintf('Unable to write to "%s".', $targetPath));
        }

        $this->stream->rewind();

        while (!$this->stream->eof()) {
            fwrite($file, $this->stream->read(PHP_INT_MAX));
        }

        fclose($file);

        $this->moved = true;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->client_filename;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->client_media_type;
    }
}
