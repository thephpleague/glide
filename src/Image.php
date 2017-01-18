<?php

namespace League\Glide;

use League\Glide\Exceptions\FileNotFoundException;
use League\Glide\Exceptions\FilesystemException;
use League\Glide\Exceptions\SignatureException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Image
{
    /**
     * Glide server.
     * @var Server
     */
    protected $server;

    /**
     * Image path.
     * @var string
     */
    protected $path;

    /**
     * Image manipulation attributes.
     * @var array
     */
    protected $attributes;

    /**
     * The request image signature.
     * @var array
     */
    protected $signature;

    /**
     * Create image.
     * @param Server $server     Glide server.
     * @param string $path       Image path.
     * @param array  $attributes Image manipulation attributes.
     * @param string $signature  The request image signature.
     */
    public function __construct(Server $server, string $path, array $attributes = [], string $signature = null)
    {
        $this->server = $server;
        $this->path = $path;
        $this->attributes = $attributes;
        $this->signature = $signature;
    }

    /**
     * Get the image source path.
     * @return string                The image source path.
     * @throws FileNotFoundException
     */
    public function getSourcePath()
    {
        if ($this->server->getSourceFolder()) {
            return $this->server->getSourceFolder().'/'.$this->path;
        }

        return $this->path;
    }

    /**
     * Check if an image source file exists.
     * @return bool Whether the source file exists.
     */
    public function sourceExists()
    {
        return $this->server->getSource()->has($this->path);
    }

    /**
     * Get the image cache path.
     * @return string                The image cache path.
     * @throws FileNotFoundException
     */
    public function getCachePath()
    {
        if ($this->server->getCacheFolder()) {
            return $this->server->getCacheFolder().'/'.$this->getCacheFilename();
        }

        return $this->getCacheFilename();
    }

    /**
     * Get the image cache filename.
     * @return string The image cache filename.
     */
    public function getCacheFilename()
    {
        $info = pathinfo($this->path);
        $filename = $info['filename'];
        $attributes = $this->attributes;

        if (!isset($attributes['fm'])) {
            $attributes['fm'] = $info['extension'];
        }

        $extension = $attributes['fm'] === 'pjpg' ? 'jpg' : $attributes['fm'];

        unset($attributes['fm']);
        ksort($attributes);

        $attributes = array_map(function ($key, $value) {
            return $key.'-'.$value;
        }, array_keys($attributes), $attributes);

        array_unshift($attributes, $filename);

        return $this->path.'/'.implode('-', $attributes).'.'.$extension;
    }

    /**
     * Check if an image cache file exists.
     * @return bool Whether the cache file exists.
     */
    public function cacheExists()
    {
        return $this->server->getCache()->has(
            $this->getCachePath()
        );
    }

    /**
     * Delete cached images.
     * @return bool Whether the delete succeeded.
     */
    public function deleteCache()
    {
        return $this->server->getCache()->deleteDir(
            $this->cacheFolder ? $this->cacheFolder.'/'.$this->path : $this->path
        );
    }

    /**
     * Get all image manipulations params, including defaults and presets.
     * @param  array $params Image manipulation params.
     * @return array All image manipulation params.
     */
    public function getAttributes()
    {
        $attributes = $this->attributes;
        $defaults = $this->server->getDefaults();
        $presets = $this->server->getPresets();

        if (isset($attributes['p'])) {
            foreach (explode(',', $attributes['p']) as $preset) {
                if (isset($presets[$preset])) {
                    $defaults = array_merge($defaults, $presets[$preset]);
                }
            }
        }

        $attributes = array_filter(array_merge($defaults, $attributes));

        if (!isset($attributes['fm'])) {
            $attributes['fm'] = 'jpg';
        }

        unset($attributes['p']);

        return $attributes;
    }

    /**
     * Get the image url.
     * @return string The image url.
     */
    public function getUrl()
    {
        $base = $this->server->getCacheUrl() ? $this->server->getCacheUrl() : $this->server->getBaseUrl();

        $url = $base.'/'.$this->getCacheFilename($this->path, $this->getAttributes());

        if ($this->server->getSignKey()) {
            $url .= '?'.http_build_query(['s' => $this->generateSignature()]);
        }

        return $url;
    }

    /**
     * Validate a signature.
     * @throws SignatureException
     * @return $this
     */
    public function validateSignature()
    {
        if ($this->generateSignature() !== $this->signature) {
            throw new SignatureException();
        }

        return $this;
    }

    /**
     * Generate a signature.
     * @return string The generated signature.
     */
    public function generateSignature()
    {
        $attributes = $this->getAttributes();

        unset($attributes['s']);
        ksort($attributes);

        return hash_hmac('sha256', ltrim($this->path, '/').'?'.http_build_query($attributes), $this->server->getSignKey());
    }

    /**
     * Generate manipulated image.
     * @throws FileNotFoundException
     * @throws FilesystemException
     * @return $this
     */
    public function makeImage()
    {
        $sourcePath = $this->getSourcePath();
        $cachedPath = $this->getCachePath();

        if ($this->cacheExists() === true) {
            return $this;
        }

        if ($this->sourceExists() === false) {
            throw new FileNotFoundException(
                'Could not find the image `'.$sourcePath.'`.'
            );
        }

        $source = $this->server->getSource()->read(
            $sourcePath
        );

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$sourcePath.'`.'
            );
        }

        // We need to write the image to the local disk before
        // doing any manipulations. This is because EXIF data
        // can only be read from an actual file.
        $tmp = tempnam(sys_get_temp_dir(), 'Glide');

        if (file_put_contents($tmp, $source) === false) {
            throw new FilesystemException(
                'Unable to write temp file for `'.$sourcePath.'`.'
            );
        }

        try {
            $write = $this->server->getCache()->write(
                $cachedPath,
                $this->server->generateImage($tmp, $this->attributes)
            );

            if ($write === false) {
                throw new FilesystemException(
                    'Could not write the image `'.$cachedPath.'`.'
                );
            }
        } catch (FileExistsException $exception) {
            // This edge case occurs when the target already exists
            // because it's currently be written to disk in another
            // request. It's best to just fail silently.
        } finally {
            unlink($tmp);
        }

        return $this;
    }

    /**
     * Generate and return Base64 encoded image.
     * @return string              Base64 encoded image.
     * @throws FilesystemException
     */
    public function getAsBase64()
    {
        $this->makeImage();

        $source = $this->server->getCache()->read(
            $this->getCachePath()
        );

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$this->path.'`.'
            );
        }

        return 'data:'.$this->server->getCache()->getMimetype($this->getCachePath()).';base64,'.base64_encode($source);
    }

    /**
     * Generate and output image.
     * @throws InvalidArgumentException
     */
    public function output()
    {
        $this->makeImage()->getResponse()->send();
    }

    /**
     * Generate and return image response.
     * @return mixed                    Image response.
     * @throws InvalidArgumentException
     */
    public function getResponse()
    {
        $stream = $this->server->getCache()->readStream(
            $this->getCachePath()
        );

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $this->server->getCache()->getMimetype($this->getCachePath()));
        $response->headers->set('Content-Length', $this->server->getCache()->getSize($this->getCachePath()));
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));

        // if ($this->request) {
        //     $response->setLastModified(date_create()->setTimestamp($this->server->getCache()->getTimestamp($this->getCachePath())));
        //     $response->isNotModified($this->request);
        // }

        $response->setCallback(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Set a manipulation attribute.
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $this->attributes[$name] = $arguments[0];

        return $this;
    }
}
