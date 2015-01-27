<?php

namespace League\Glide\Api;

use League\Glide\Api\Manipulator\Size as SizeManipulator;

class ApiFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateApiFactory()
    {
        $this->assertInstanceOf('League\Glide\Api\ApiFactory', new ApiFactory([]));
    }

    public function testGetApi()
    {
        $server = new ApiFactory();

        $this->assertInstanceOf('League\Glide\Api\Api', $server->getApi());
    }

    public function testGetImageManager()
    {
        $server = new ApiFactory([
            'driver' => 'imagick',
        ]);

        $this->assertEquals('imagick', $server->getImageManager()->config['driver']);
    }

    public function testGetManipulators()
    {
        $server = new ApiFactory([]);

        $this->assertInternalType('array', $server->getManipulators());
    }

    public function testGetManipulatorsWithMaxImageSize()
    {
        $server = new ApiFactory([
            'max_image_size' => 2000*2000,
        ]);

        $sizeManipulator = null;

        foreach ($server->getManipulators() as $manipulator) {
            if ($manipulator instanceof SizeManipulator) {
                $sizeManipulator = $manipulator;
            }
        }

        $this->assertInstanceOf('League\Glide\Api\Manipulator\Size', $sizeManipulator);
        $this->assertEquals(2000*2000, $sizeManipulator->getMaxImageSize());
    }

    public function testCreate()
    {
        $this->assertInstanceOf('League\Glide\Api\Api', ApiFactory::create());
    }
}
