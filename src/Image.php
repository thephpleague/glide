<?php

namespace League\Glide;

use League\Flysystem\FileExistsException;
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
     * Create image.
     * @param Server $server     Glide server.
     * @param string $path       Image path.
     * @param array  $attributes Image manipulation attributes.
     */
    public function __construct(Server $server, string $path, array $attributes = [])
    {
        $this->server = $server;
        $this->path = $path;
        $this->attributes = $attributes;
    }

    /**
     * Get the image source path.
     * @return string The image source path.
     */
    public function sourcePath()
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
     * @return string The image cache path.
     */
    public function cachePath()
    {
        if ($this->server->getCacheFolder()) {
            return $this->server->getCacheFolder().'/'.$this->cacheFilename();
        }

        return $this->cacheFilename();
    }

    /**
     * Get the image cache filename.
     * @return string The image cache filename.
     */
    public function cacheFilename()
    {
        return $this->path.'/'.$this->signature().'/'.pathinfo($this->path)['filename'].'.'.($this->attributes()['fm'] ?? 'jpg');
    }

    /**
     * Check if an image cache file exists.
     * @return bool Whether the cache file exists.
     */
    public function cacheExists()
    {
        return $this->server->getCache()->has(
            $this->cachePath()
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
    public function attributes()
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

        unset($attributes['p']);

        return $attributes;
    }

    /**
     * Get the image url.
     * @return string The image url.
     */
    public function url()
    {
        $baseUrl = $this->server->getCacheUrl() ? $this->server->getCacheUrl() : $this->server->getBaseUrl();

        return $baseUrl.'/'.$this->cacheFilename().'?'.http_build_query($this->attributes());
    }

    /**
     * Validate a signature.
     * @throws SignatureException
     * @return $this
     */
    public function validateSignature($signature)
    {
        if ($this->signature() !== $signature) {
            throw new SignatureException('Not a valid signature.');
        }

        return $this;
    }

    /**
     * Generate a signature.
     * @return string The generated signature.
     */
    public function signature()
    {
        if (!$this->server->getSignKey()) {
            throw new InvalidArgumentException('A signature sign key has not been set.');
        }

        $attributes = $this->attributes();

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
    public function generate()
    {
        if ($this->cacheExists() === true) {
            return $this;
        }

        if ($this->sourceExists() === false) {
            throw new FileNotFoundException('Could not find the image `'.$this->sourcePath().'`.');
        }

        $source = $this->server->getSource()->read(
            $this->sourcePath()
        );

        if ($source === false) {
            throw new FilesystemException('Could not read the image `'.$this->sourcePath().'`.');
        }

        try {
            // We need to write the image to the local disk before
            // doing any manipulations. This is because EXIF data
            // can only be read from an actual file.
            $tmp = tempnam(sys_get_temp_dir(), 'Glide');

            if (file_put_contents($tmp, $source) === false) {
                throw new FilesystemException('Unable to write temp file for `'.$this->sourcePath().'`.');
            }

            $image = $this->server->getImageManager()->make($tmp);

            foreach ($this->server->getManipulators() as $manipulator) {
                $manipulator->setParams($this->attributes);

                $image = $manipulator->run($image);
            }

            $write = $this->server->getCache()->write(
                $this->cachePath(),
                $image->getEncoded()
            );

            if ($write === false) {
                throw new FilesystemException('Could not write the image `'.$this->cachePath().'`.');
            }
        } catch (FileExistsException $exception) {
            // This edge case occurs when the target already exists
            // because it's currently be written to disk in another
            // request. It's best to just fail silently.
        } finally {
            @unlink($tmp);
        }

        return $this;
    }

    /**
     * Generate and return Base64 encoded image.
     * @return string              Base64 encoded image.
     * @throws FilesystemException
     */
    public function base64()
    {
        $this->generate();

        $source = $this->server->getCache()->read(
            $this->cachePath()
        );

        if ($source === false) {
            throw new FilesystemException('Could not read the image `'.$this->path.'`.');
        }

        return 'data:'.$this->server->getCache()->getMimetype($this->cachePath()).';base64,'.base64_encode($source);
    }

    /**
     * Generate and output image.
     */
    public function output()
    {
        $this->generate()->response()->send();
    }

    /**
     * Generate and return image response.
     * @param  Request $request Optional request.
     * @return mixed   Image response.
     */
    public function response(Request $request = null)
    {
        $stream = $this->server->getCache()->readStream(
            $this->cachePath()
        );

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $this->server->getCache()->getMimetype($this->cachePath()));
        $response->headers->set('Content-Length', $this->server->getCache()->getSize($this->cachePath()));
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));

        if ($request) {
            $response->setLastModified(
                date_create()->setTimestamp(
                    $this->server->getCache()->getTimestamp(
                        $this->cachePath()
                    )
                )
            );
            $response->isNotModified($request);
        }

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
