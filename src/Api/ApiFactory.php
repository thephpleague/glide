<?php

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use League\Glide\Api\Manipulator\Blur;
use League\Glide\Api\Manipulator\Brightness;
use League\Glide\Api\Manipulator\Contrast;
use League\Glide\Api\Manipulator\Filter;
use League\Glide\Api\Manipulator\Gamma;
use League\Glide\Api\Manipulator\Orientation;
use League\Glide\Api\Manipulator\Output;
use League\Glide\Api\Manipulator\Pixelate;
use League\Glide\Api\Manipulator\Rectangle;
use League\Glide\Api\Manipulator\Sharpen;
use League\Glide\Api\Manipulator\Size;

class ApiFactory
{
    /**
     * Configuration parameters.
     * @var array
     */
    protected $config;

    /**
     * Create Api factory instance.
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the image manipulation API.
     * @return Api The image manipulation API.
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
     * Get the default manipulators.
     * @return array Collection of manipulators.
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

    /**
     * Create Api instance.
     * @param  array $config Configuration parameters.
     * @return Api   The configured Api.
     */
    public static function create(array $config = [])
    {
        return (new self($config))->getApi();
    }
}
