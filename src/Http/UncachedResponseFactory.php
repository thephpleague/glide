<?php

namespace League\Glide\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UncachedResponseFactory
{
    /**
     * The final image's binary string.
     * @var string
     */
    protected $inMemoryImage;

    /**
     * The request object.
     * @var Request
     */
    protected $request;

    /**
     * The timestamp representation of the source file's last modified date.
     * @var integer
     */
    protected $sourceLastModified;

    /**
     * Create Output instance.
     * @param string  $cache              The cache file system.
     * @param Request $request            The request object.
     * @param integer $sourceLastModified Timestamp
     */
    public function __construct($imageBinary, Request $request, $sourceLastModified)
    {
        $this->inMemoryImage      = $imageBinary;
        $this->request            = $request;
        $this->sourceLastModified = $sourceLastModified;
    }

    /**
     * Get the streamed response.
     * @return StreamedResponse The response object.
     */
    public function getResponse()
    {
        $response = new StreamedResponse();

        $this->setHeaders($response);
        $this->setContent($response);

        return $response;
    }

    /**
     * Set the streamed response headers.
     * @param  StreamedResponse $response The response object.
     * @return StreamedResponse
     */
    public function setHeaders(StreamedResponse $response)
    {
        $finfo       = new \Finfo(FILEINFO_MIME_TYPE);
        $contentType = $finfo->buffer($this->inMemoryImage);

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Length', strlen($this->inMemoryImage));

        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));
        $response->setLastModified(date_create()->setTimestamp($this->sourceLastModified));
        $response->isNotModified($this->request);

        return $response;
    }

    /**
     * Set the stream response content.
     * @param  StreamedResponse $response The response object.
     * @return StreamedResponse
     */
    public function setContent(StreamedResponse $response)
    {
        $response->setCallback(function() {
            echo $this->inMemoryImage;
        });

        return $response;
    }

    /**
     * Create response instance.
     * @param  string           $cache              The cache file system.
     * @param  Request          $request            The request object.
     * @param  integer          $sourceLastModified Timestamp
     * @return StreamedResponse The response object.
     */
    public static function create($imageBinary, Request $request, $sourceLastModified)
    {
        return (new self($imageBinary, $request, $sourceLastModified))->getResponse();
    }
}
