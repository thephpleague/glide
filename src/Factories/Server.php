<?php

namespace Glide\Factories;

use Glide\Api;
use Glide\Manipulators\Blur;
use Glide\Manipulators\Brightness;
use Glide\Manipulators\Contrast;
use Glide\Manipulators\Filter;
use Glide\Manipulators\Gamma;
use Glide\Manipulators\Orientation;
use Glide\Manipulators\Output;
use Glide\Manipulators\Pixelate;
use Glide\Manipulators\Rectangle;
use Glide\Manipulators\Sharpen;
use Glide\Manipulators\Size;
use Glide\Server as GlideServer;
use Glide\SignKey;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class Server
{
    private $config;

    /**
     * Create server factory instance.
     * @param Array $config Configuration parameters.
     */
    public function __construct(Array $config)
    {
        $this->config = $config;
    }

    /**
     * Create server instance.
     * @return GlideServer The configured Glide server.
     */
    public function make()
    {
        return new GlideServer(
            $this->getSource(),
            $this->getCache(),
            $this->getApi(),
            $this->getSignKey()
        );
    }

    /**
     * Get the source file system.
     * @return FilesystemInterface The source file system.
     */
    public function getSource()
    {
        $source = null;

        if (isset($this->config['source'])) {
            $source = $this->config['source'];
        }

        if (is_string($source)) {
            return new Filesystem(new Local($source));
        }

        if ($source instanceof FilesystemInterface) {
            return $source;
        }

        throw new InvalidArgumentException('Invalid `source` parameter.');
    }

    /**
     * Get the cache file system.
     * @return FilesystemInterface The cache file system.
     */
    public function getCache()
    {
        $cache = null;

        if (isset($this->config['cache'])) {
            $cache = $this->config['cache'];
        }

        if (is_string($cache)) {
            return new Filesystem(new Local($cache));
        }

        if ($cache instanceof FilesystemInterface) {
            return $cache;
        }

        throw new InvalidArgumentException('Invalid `cache` parameter.');
    }

    /**
     * Get the image manipulation API.
     * @return ApiInterface The image manipulation API.
     */
    public function getApi()
    {
        return new Api(
            $this->getImageManager(),
            $this->getManipulators()
        );
    }

    /**
     * Get the image manager.
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager()
    {
        $driver = 'gd';

        if (isset($this->config['driver'])) {
            $driver = $this->config['driver'];
        }

        return new ImageManager([
            'driver' => $driver,
        ]);
    }

    /**
     * Get the sign key.
     * @return SignKey Secret key used to secure URLs.
     */
    public function getSignKey()
    {
        $signKey = null;

        if (isset($this->config['sign_key'])) {
            $signKey = new SignKey($this->config['sign_key']);
        }

        return $signKey;
    }

    /**
     * Get the default manipulators.
     * @return SignKey Collection of manipulators.
     */
    public function getManipulators()
    {
        $maxImageSize = null;

        if (isset($this->config['max_image_size'])) {
            $maxImageSize = $this->config['max_image_size'];
        }

        return [
            new Orientation(),
            new Rectangle(),
            new Size($maxImageSize),
            new Brightness(),
            new Contrast(),
            new Gamma(),
            new Sharpen(),
            new Filter(),
            new Blur(),
            new Pixelate(),
            new Output(),
        ];
    }
}
