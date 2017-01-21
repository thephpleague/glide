<?php

namespace League\Glide;

use Intervention\Image\ImageManager;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\RequestInterface as Psr7Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Server
{
    /**
     * Intervention image manager.
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * Collection of manipulators.
     * @var array
     */
    protected $manipulators;

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
    protected $key;

    /**
     * Response type.
     * @var string
     */
    protected $responseType = 'httpfoundation';

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
     * Create server.
     * @param ImageManager               $imageManager Intervention image manager.
     * @param array                      $manipulators Collection of manipulators.
     * @param FilesystemInterface|string $source       Source file system.
     * @param FilesystemInterface|string $source       Cache file system.
     * @param string                     $key          Sign key.
     */
    public function __construct(ImageManager $imageManager, array $manipulators, $source, $cache)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
        $this->setSource($source);
        $this->setCache($cache);
    }

    /**
     * Set the image manager.
     * @param ImageManager $imageManager Intervention image manager.
     */
    public function setImageManager(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;

        return $this;
    }

    /**
     * Get the image manager.
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager()
    {
        return $this->imageManager;
    }

    /**
     * Set the manipulators.
     * @param array $manipulators Collection of manipulators.
     */
    public function setManipulators(array $manipulators)
    {
        foreach ($manipulators as $manipulator) {
            if (!is_a($manipulator, Manipulators\ManipulatorInterface::class)) {
                throw new InvalidArgumentException('Not a valid manipulator: '.get_class($manipulator));
            }
        }

        $this->manipulators = $manipulators;

        return $this;
    }

    /**
     * Get the manipulators.
     * @return array Collection of manipulators.
     */
    public function getManipulators()
    {
        return $this->manipulators;
    }

    /**
     * Set source file system.
     * @param FilesystemInterface|string $source Source file system.
     */
    public function setSource($source)
    {
        if (is_string($source)) {
            $source = new Filesystem(
                new Local($source)
            );
        }

        if (!is_a($source, FilesystemInterface::class)) {
            throw new InvalidArgumentException('A valid "source" file system is required.');
        }

        $this->source = $source;

        return $this;
    }

    /**
     * Get source file system.
     * @return FilesystemInterface Source file system.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set source folder.
     * @param string $sourceFolder Source folder.
     */
    public function setSourceFolder($sourceFolder)
    {
        $this->sourceFolder = trim($sourceFolder, '/');

        return $this;
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
     * Set base URL.
     * @param string $baseUrl Base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = trim($baseUrl, '/');

        return $this;
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

        return $this;
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
     * @param FilesystemInterface|string $cache Cache file system.
     */
    public function setCache($cache)
    {
        if (is_string($cache)) {
            $cache = new Filesystem(
                new Local($cache)
            );
        }

        if (!is_a($cache, FilesystemInterface::class)) {
            throw new InvalidArgumentException('A valid "cache" file system is required.');
        }

        $this->cache = $cache;

        return $this;
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

        return $this;
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
     * Set the sign key.
     * @param string $key The sign key.
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the sign key.
     * @return string The sign key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the response type.
     * @param string $responseType The response type.
     */
    public function setResponseType($responseType)
    {
        if (!in_array($responseType, ['httpfoundation', 'psr7'], true)) {
            throw new InvalidArgumentException('Not a valid response type: '.$responseType);
        }

        $this->responseType = $responseType;

        return $this;
    }

    /**
     * Get the response type.
     * @return string The response type.
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Set default image manipulations.
     * @param array $defaults Default image manipulations.
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
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

        return $this;
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
     * Create image.
     * @param  string $path       Image path.
     * @param  array  $attributes Image manipulation attributes.
     * @return Image  The image.
     */
    public function fromPath($path, $attributes = [])
    {
        return new Image($this, $path, $attributes);
    }

    /**
     * Create Image from a request.
     * @param  HttpFoundationRequest|Psr7Request $request The request.
     * @return Image                             The image.
     */
    public function fromRequest($request = null)
    {
        $request = $request ?? HttpFoundationRequest::createFromGlobals();

        if (is_a($request, HttpFoundationRequest::class)) {
            $path = $request->getPathInfo();
            $attributes = $request->query->all();
        } elseif (is_a($request, Psr7Request::class)) {
            $path = $request->getUri()->getPath();
            $attributes = $request->getQueryParams();
        } else {
            throw new InvalidArgumentException('Not a valid request.');
        }

        $path = array_filter(explode('/', $path));
        $filename = array_pop($path);
        $signature = array_pop($path);
        $path = implode('/', $path);
        $baseUrl = trim($this->baseUrl, '/').'/';

        if (substr($path, 0, strlen($baseUrl)) === $baseUrl) {
            $path = substr($path, strlen($baseUrl));
        }

        $image = new Image($this, $path, $attributes);
        $image->validateSignature($signature);

        return $image;
    }

    /**
     * Create a configured server.
     * @param  array  $config The configuration parameters.
     * @return Server Configured server.
     */
    public static function create(array $config = [])
    {
        $manipulators = [
            new Manipulators\Orientation(),
            new Manipulators\Crop(),
            new Manipulators\Size($config['max_image_size'] ?? null),
            new Manipulators\Brightness(),
            new Manipulators\Contrast(),
            new Manipulators\Gamma(),
            new Manipulators\Sharpen(),
            new Manipulators\Filter(),
            new Manipulators\Blur(),
            new Manipulators\Pixelate(),
            new Manipulators\Watermark($config['watermarks'] ?? null, $config['watermarks_folder'] ?? null),
            new Manipulators\Background(),
            new Manipulators\Border(),
            new Manipulators\Encode(),
        ];

        $server = new self(
            new ImageManager(['driver' => $config['driver'] ?? 'gd']),
            $manipulators,
            $config['source'] ?? null,
            $config['cache'] ?? null
        );

        unset(
            $config['driver'],
            $config['max_image_size'],
            $config['watermarks'],
            $config['watermarks_folder']
        );

        foreach ($config as $setting => $value) {
            $server->{'set'.str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $setting)))}($value);
        }

        return $server;
    }
}
