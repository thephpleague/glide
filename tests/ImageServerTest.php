<?php

namespace League\Glide;

use Mockery;

class ImageServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');

        $responseFactory = Mockery::mock('League\Glide\Responses\ResponseFactoryInterface');
        $responseFactory
            ->shouldReceive('create')
            ->andReturn($response)
            ->shouldReceive('send')
            ->andReturnUsing(function () {
                echo 'content';
            });

        $this->server = new ImageServer(
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Glide\Api\ApiInterface'),
            $responseFactory
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\ImageServer', $this->server);
    }

    public function testSetSource()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getSource());
    }

    public function testGetSource()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getSource());
    }

    public function testSetSourcePathPrefix()
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePathPrefix()
    {
        $this->assertEquals('', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePath()
    {
        $this->assertEquals('image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('image.jpg', $this->server->getSourcePath('img/image.jpg'));
    }

    public function testGetSourcePathWithPrefix()
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img/image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithMissingPath()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FileNotFoundException',
            'Image path missing.'
        );

        $this->server->getSourcePath('');
    }

    public function testGetSourcePathWithEncodedEntities()
    {
        $this->assertEquals('an image.jpg', $this->server->getSourcePath('an%20image.jpg'));
    }

    public function testSourceFileExists()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->sourceFileExists('image.jpg'));
    }

    public function testSetBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('img', $this->server->getBaseUrl());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('', $this->server->getBaseUrl());
    }

    public function testSetCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getCache());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getCache());
    }

    public function testSetCachePathPrefix()
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img', $this->server->getCachePathPrefix());
    }

    public function testGetCachePathPrefix()
    {
        $this->assertEquals('', $this->server->getCachePathPrefix());
    }

    public function testSetGroupCacheInFolders()
    {
        $this->server->setGroupCacheInFolders(false);

        $this->assertFalse($this->server->getGroupCacheInFolders());
    }

    public function testGetGroupCacheInFolders()
    {
        $this->assertTrue($this->server->getGroupCacheInFolders());
    }

    public function testSetCacheWithFileExtensions()
    {
        $this->server->setCacheWithFileExtensions(true);

        $this->assertTrue($this->server->getCacheWithFileExtensions());
    }

    public function testGetCacheWithFileExtensions()
    {
        $this->assertFalse($this->server->getCacheWithFileExtensions());
    }

    public function testGetCachePath()
    {
        $this->assertEquals(
            'image.jpg/e863e008b6f09807c3b0aa3805bc9c63',
            $this->server->getCachePath('image.jpg', ['w' => '100'])
        );
    }

    public function testGetCachePathWithNoFolderGrouping()
    {
        $this->server->setGroupCacheInFolders(false);

        $this->assertEquals(
            'e863e008b6f09807c3b0aa3805bc9c63',
            $this->server->getCachePath('image.jpg', ['w' => '100'])
        );
    }

    public function testGetCachePathWithPrefix()
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img/image.jpg/75094881e9fd2b93063d6a5cb083091c', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithSourcePrefix()
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('image.jpg/75094881e9fd2b93063d6a5cb083091c', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtension()
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->assertEquals('image.jpg/75094881e9fd2b93063d6a5cb083091c.jpg', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtensionAndFmParam()
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->assertEquals('image.jpg/eb6091e07fb06219634a3c82afb88239.gif', $this->server->getCachePath('image.jpg', ['fm' => 'gif']));
    }

    public function testGetCachePathWithExtensionAndFmFromDefaults()
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->server->setDefaults(['fm' => 'gif']);
        $this->assertEquals('image.jpg/eb6091e07fb06219634a3c82afb88239.gif', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtensionAndFmFromPreset()
    {
        $this->server->setCacheWithFileExtensions(true);

        $this->server->setPresets(['gif' => [
            'fm' => 'gif',
        ]]);

        $this->assertEquals('image.jpg/eb6091e07fb06219634a3c82afb88239.gif', $this->server->getCachePath('image.jpg', ['p' => 'gif']));
    }

    public function testCacheFileExists()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('image.jpg/75094881e9fd2b93063d6a5cb083091c')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->cacheFileExists('image.jpg', []));
    }

    public function testDeleteCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('deleteDir')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->deleteCache('image.jpg', []));
    }

    public function testDeleteCacheWithGroupCacheInFoldersDisabled()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Deleting cached image manipulations is not possible when grouping cache into folders is disabled.'
        );

        $this->server->setGroupCacheInFolders(false);

        $this->server->deleteCache('image.jpg', []);
    }

    public function testSetApi()
    {
        $api = Mockery::mock('League\Glide\Api\ApiInterface');
        $this->server->setApi($api);
        $this->assertInstanceOf('League\Glide\Api\ApiInterface', $this->server->getApi());
    }

    public function testGetApi()
    {
        $this->assertInstanceOf('League\Glide\Api\ApiInterface', $this->server->getApi());
    }

    public function testSetDefaults()
    {
        $defaults = [
            'fm' => 'jpg',
        ];

        $this->server->setDefaults($defaults);

        $this->assertSame($defaults, $this->server->getDefaults());
    }

    public function testGetDefaults()
    {
        $this->testSetDefaults();
    }

    public function testSetPresets()
    {
        $presets = [
            'small' => [
                'w' => '200',
                'h' => '200',
                'fit' => 'crop',
            ],
        ];

        $this->server->setPresets($presets);

        $this->assertSame($presets, $this->server->getPresets());
    }

    public function testGetPresets()
    {
        $this->testSetPresets();
    }

    public function testGetAllParams()
    {
        $this->server->setDefaults([
            'fm' => 'jpg',
        ]);

        $this->server->setPresets([
            'small' => [
                'w' => '200',
                'h' => '200',
                'fit' => 'crop',
            ],
        ]);

        $all_params = $this->server->getAllParams([
            'w' => '100',
            'p' => 'small',
        ]);

        $this->assertSame([
            'fm' => 'jpg',
            'w' => '100',
            'h' => '200',
            'fit' => 'crop',
            'p' => 'small',
        ], $all_params);
    }

    public function testSetResponseFactory()
    {
        $this->server->setResponseFactory(Mockery::mock('League\Glide\Responses\ResponseFactoryInterface'));

        $this->assertInstanceOf(
            'League\Glide\Responses\ResponseFactoryInterface',
            $this->server->getResponseFactory()
        );
    }

    public function testGetResponseFactory()
    {
        $this->testSetResponseFactory();
    }

    public function testGetImageResponse()
    {
        $this->server->setResponseFactory(Mockery::mock('League\Glide\Responses\ResponseFactoryInterface', function ($mock) {
            $mock->shouldReceive('create')->andReturn(Mockery::mock('Psr\Http\Message\ResponseInterface'));
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf(
            'Psr\Http\Message\ResponseInterface',
            $this->server->getImageResponse('image.jpg', [])
        );
    }

    public function testGetImageResponseWithoutResponseFactory()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Unable to get image response, no response factory defined.'
        );

        $this->server->getImageResponse('image.jpg', []);
    }

    public function testGetImageAsBase64()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'data:image/jpeg;base64,Y29udGVudA==',
            $this->server->getImageAsBase64('image.jpg', [])
        );
    }

    public function testGetImageAsBase64WithUnreadableSource()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('read')->andReturn(false)->once();
        }));

        $this->setExpectedException(
            'League\Glide\Filesystem\FilesystemException',
            'Could not read the image `image.jpg/75094881e9fd2b93063d6a5cb083091c`.'
        );

        $this->server->getImageAsBase64('image.jpg', []);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutputImage()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);

            $file = tmpfile();
            fwrite($file, 'content');
            $mock->shouldReceive('readStream')->andReturn($file);
        }));

        ob_start();
        $response = $this->server->outputImage('image.jpg', []);
        $content = ob_get_clean();

        $this->assertNull($response);
        $this->assertEquals('content', $content);
    }

    public function testMakeImageFromSource()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->with('image.jpg/75094881e9fd2b93063d6a5cb083091c', 'content')->once();
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromSourceThatDoesNotExist()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FileNotFoundException',
            'Could not find the image `image.jpg`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnreadableSource()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FilesystemException',
            'Could not read the image `image.jpg`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn(false)->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnwritableCache()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FilesystemException',
            'Could not write the image `image.jpg/75094881e9fd2b93063d6a5cb083091c`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->andReturn(false)->once();
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithExistingCacheFile()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->andThrow(new \League\Flysystem\FileExistsException('75094881e9fd2b93063d6a5cb083091c'));
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }
}
