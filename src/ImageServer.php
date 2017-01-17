<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Responses\ResponseFactoryInterface;

class ImageServer
{
    /**
     * Source file system.
     * @var FilesystemInterface
     */
    protected $source;

    /**
     * Source folder.
     * @var string
     */
    protected $sourceFolder;

    /**
     * Cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

    /**
     * Cache folder.
     * @var string
     */
    protected $cacheFolder;

    /**
     * Image manipulation API.
     * @var ApiInterface
     */
    protected $api;

    /**
     * Response factory.
     * @var ResponseFactoryInterface|null
     */
    protected $responseFactory;

    /**
     * Base URL.
     * @var string
     */
    protected $baseUrl;

    /**
     * Cache URL.
     * @var string
     */
    protected $cacheUrl;

    /**
     * Sign key.
     * @var string
     */
    protected $signKey;

    /**
     * Default image manipulations.
     * @var array
     */
    protected $defaults = [];

    /**
     * Preset image manipulations.
     * @var array
     */
    protected $presets = [];

    /**
     * Create Server instance.
     * @param FilesystemInterface $source  Source file system.
     * @param FilesystemInterface $cache   Cache file system.
     * @param ApiInterface        $api     Image manipulation API.
     * @param string              $signKey Sign key.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, ApiInterface $api, $signKey)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
        $this->setSignKey($signKey);
    }

    /**
     * Set source file system.
     * @param FilesystemInterface $source Source file system.
     */
    public function setSource(FilesystemInterface $source)
    {
        $this->source = $source;
    }

    /**
     * Get source file system.
     * @return FilesystemInterface Source file system.
     */
    public function getSource()
    {
        return $this->source;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * Set source folder.
     * @param string $sourceFolder Source folder.
     */
    public function setSourceFolder($sourceFolder)
    {
        $this->sourceFolder = trim($sourceFolder, '/');
    }

    /**
     * Get source folder.
     * @return string Source folder.
     */
    public function getSourceFolder()
    {
        return $this->sourceFolder;
    }

    /**
     * Get source path.
     * @param  string                $path Image path.
     * @return string                The source path.
     * @throws FileNotFoundException
     */
    public function getSourcePath($path)
    {
        if ($this->sourceFolder) {
            $path = $this->sourceFolder.'/'.$path;
        }

        return rawurldecode($path);
    }

    /**
     * Check if a source file exists.
     * @param  string $path Image path.
     * @return bool   Whether the source file exists.
     */
    public function sourceFileExists($path)
    {
        return $this->source->has($this->getSourcePath($path));
    }

    /**
     * Set base URL.
     * @param string $baseUrl Base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = trim($baseUrl, '/');
    }

    /**
     * Get base URL.
     * @return string Base URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set cache URL.
     * @param string $cacheUrl Cache URL.
     */
    public function setCacheUrl($cacheUrl)
    {
        $this->cacheUrl = trim($cacheUrl, '/');
    }

    /**
     * Get cache URL.
     * @return string Cache URL.
     */
    public function getCacheUrl()
    {
        return $this->cacheUrl;
    }

    /**
     * Set cache file system.
     * @param FilesystemInterface $cache Cache file system.
     */
    public function setCache(FilesystemInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get cache file system.
     * @return FilesystemInterface Cache file system.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set cache folder.
     * @param string $cacheFolder Cache folder.
     */
    public function setCacheFolder($cacheFolder)
    {
        $this->cacheFolder = trim($cacheFolder, '/');
    }

    /**
     * Get cache folder.
     * @return string Cache folder.
     */
    public function getCacheFolder()
    {
        return $this->cacheFolder;
    }

    /**
     * Get cache path.
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return string Cache path.
     */
    public function getCachePath($path, array $params = [])
    {
        $filename = $this->getCacheFilename($path, $params);

        return $this->cacheFolder ? $this->cacheFolder.'/'.$filename : $filename;
    }

    public function getCacheFilename($path, array $params = [])
    {
        $filename = pathinfo($path)['filename'];
        $signature = $this->generateSignature($path, $params);
        $extension = $params['fm'] === 'pjpg' ? 'jpg' : $params['fm'];

        unset($params['s'], $params['fm']);
        ksort($params);

        $params = array_map(function ($key, $value) {
            return $key.'-'.$value;
        }, array_keys($params), $params);

        array_unshift($params, $filename);

        return $path.'/'.$signature.'/'.implode('-', $params).'.'.$extension;
    }

    /**
     * Check if a cache file exists.
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return bool   Whether the cache file exists.
     */
    public function cacheFileExists($path, array $params = [])
    {
        return $this->cache->has(
            $this->getCachePath($path, $params)
        );
    }

    /**
     * Delete cached manipulations for an image.
     * @param  string $path Image path.
     * @return bool   Whether the delete succeeded.
     */
    public function deleteCache($path)
    {
        return $this->cache->deleteDir(
            $this->cacheFolder ? $this->cacheFolder.'/'.$path : $path
        );
    }

    /**
     * Set image manipulation API.
     * @param ApiInterface $api Image manipulation API.
     */
    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get image manipulation API.
     * @return ApiInterface Image manipulation API.
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set default image manipulations.
     * @param array $defaults Default image manipulations.
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set preset image manipulations.
     * @param array $presets Preset image manipulations.
     */
    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    /**
     * Get preset image manipulations.
     * @return array Preset image manipulations.
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Set response factory.
     * @param ResponseFactoryInterface|null $responseFactory Response factory.
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory = null)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get response factory.
     * @return ResponseFactoryInterface Response factory.
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * Generate and return image response.
     * @param  string                   $path   Image path.
     * @param  array                    $params Image manipulation params.
     * @return mixed                    Image response.
     * @throws InvalidArgumentException
     */
    public function getImageResponse($path, array $params = [])
    {
        if (is_null($this->responseFactory)) {
            throw new InvalidArgumentException(
                'Unable to get image response, no response factory defined.'
            );
        }

        $path = $this->makeImage($path, $params);

        return $this->responseFactory->create($this->cache, $path);
    }

    /**
     * Generate and return Base64 encoded image.
     * @param  string              $path   Image path.
     * @param  array               $params Image manipulation params.
     * @return string              Base64 encoded image.
     * @throws FilesystemException
     */
    public function getImageAsBase64($path, array $params = [])
    {
        $path = $this->makeImage($path, $params);

        $source = $this->cache->read($path);

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$path.'`.'
            );
        }

        return 'data:'.$this->cache->getMimetype($path).';base64,'.base64_encode($source);
    }

    /**
     * Generate and output image.
     * @param  string                   $path   Image path.
     * @param  array                    $params Image manipulation params.
     * @throws InvalidArgumentException
     */
    public function outputImage($path, array $params = [])
    {
        $path = $this->makeImage($path, $params);

        header('Content-Type:'.$this->cache->getMimetype($path));
        header('Content-Length:'.$this->cache->getSize($path));
        header('Cache-Control:'.'max-age=31536000, public');
        header('Expires:'.date_create('+1 years')->format('D, d M Y H:i:s').' GMT');

        $stream = $this->cache->readStream($path);

        if (ftell($stream) !== 0) {
            rewind($stream);
        }
        fpassthru($stream);
        fclose($stream);
    }

    /**
     * Generate manipulated image.
     * @param  string                $path   Image path.
     * @param  array                 $params Image manipulation params.
     * @return string                Cache path.
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function makeImage($path, array $params = [])
    {
        if (substr(ltrim($path, '/'), 0, strlen($this->baseUrl)) === $this->baseUrl) {
            $path = trim(substr(ltrim($path, '/'), strlen($this->baseUrl)), '/');
        }

        $this->validateSignature($path, $params);

        $sourcePath = $this->getSourcePath($path);
        $cachedPath = $this->getCachePath($path, $params);

        if ($this->cacheFileExists($path, $params) === true) {
            return $cachedPath;
        }

        if ($this->sourceFileExists($path) === false) {
            throw new FileNotFoundException(
                'Could not find the image `'.$sourcePath.'`.'
            );
        }

        $source = $this->source->read(
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
            $write = $this->cache->write(
                $cachedPath,
                $this->api->run($tmp, $params)
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
        }

        unlink($tmp);

        return $cachedPath;
    }

    public function parseFilenameUrl($path)
    {
        $info = pathinfo($path);
        $path = explode('/', trim($info['dirname'], '/'));
        $params['s'] = array_pop($path);
        $path = implode('/', $path);
        $params['fm'] = $info['extension'];
        $filename = $info['filename'];
        $info = pathinfo($path);
        $filename = trim(substr($filename, strlen($info['filename'])), '-');

        foreach (array_chunk(explode('-', $filename), 2) as $param) {
            if (isset($param[0]) and isset($param[1])) {
                $params[$param[0]] = $param[1];
            }
        }

        return [$path, $params];
    }

    /**
     * Get all image manipulations params, including defaults and presets.
     * @param  array $params Image manipulation params.
     * @return array All image manipulation params.
     */
    public function getAllParams(array $params = [])
    {
        $all = $this->defaults;

        if (isset($params['p'])) {
            foreach (explode(',', $params['p']) as $preset) {
                if (isset($this->presets[$preset])) {
                    $all = array_merge($all, $this->presets[$preset]);
                }
            }
        }

        $params = array_filter(array_merge($all, $params));

        if (!isset($params['fm'])) {
            $params['fm'] = 'jpg';
        }

        unset($params['p']);

        return $params;
    }

    public function getFilenameUrl($path, array $params = [])
    {
        $params = $this->getAllParams($params);

        $base = $this->cacheUrl ? $this->cacheUrl : $this->baseUrl;

        return $base.'/'.$this->getCacheFilename($path, $params);
    }

    public function getQueryStringUrl($path, array $params = [])
    {
        $params = $this->getAllParams($params);

        $base = $this->cacheUrl ? $this->cacheUrl : $this->baseUrl;

        $params['s'] = $this->generateSignature($path, $params);

        return $base.'/'.$path.'?'.http_build_query($params);
    }

    public function generateSignature($path, array $params = [])
    {
        unset($params['s']);
        ksort($params);

        return hash_hmac('sha256', ltrim($path, '/').'?'.http_build_query($params), $this->signKey);
    }

    public function validateSignature($path, $params)
    {
        if (!isset($params['s']) or $params['s'] !== $this->generateSignature($path, $params)) {
            throw new SignatureException();
        }
    }

    /**
     * Create configured server.
     * @param  array  $config Configuration parameters.
     * @return Server Configured server.
     */
    public static function create(array $config = [])
    {
        return (new ImageServerFactory($config))->create();
    }
}
