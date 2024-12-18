<?php

declare(strict_types=1);

namespace League\Glide;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Responses\ResponseFactoryInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ServerTest extends TestCase
{
    private Server $server;

    public function setUp(): void
    {
        $this->server = new Server(
            \Mockery::mock(FilesystemOperator::class),
            \Mockery::mock(FilesystemOperator::class),
            \Mockery::mock(ApiInterface::class, function ($mock) {
                $mock->shouldReceive('run')->andReturn('content');
                $mock->shouldReceive('getApiParams')->andReturn(['p', 'q', 'fm', 's', 'w', 'h', 'fit', 'crop', 'dpr']);
            })
        );
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Server::class, $this->server);
    }

    public function testSetSource(): void
    {
        $this->server->setSource(\Mockery::mock(FilesystemOperator::class));
        $this->assertInstanceOf(FilesystemOperator::class, $this->server->getSource());
    }

    public function testGetSource(): void
    {
        $this->assertInstanceOf(FilesystemOperator::class, $this->server->getSource());
    }

    public function testSetSourcePathPrefix(): void
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePathPrefix(): void
    {
        $this->assertEquals('', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePath(): void
    {
        $this->assertEquals('image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithBaseUrl(): void
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('image.jpg', $this->server->getSourcePath('img/image.jpg'));

        // Test for a bug where if the path starts with the same substring as the base url, the source
        // path would trim the base url off the filename. eg, the following would've returned 'ur.jpg'
        $this->assertEquals('imgur.jpg', $this->server->getSourcePath('imgur.jpg'));
    }

    public function testGetSourcePathWithPrefix(): void
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img/image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithMissingPath(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('Image path missing.');

        $this->server->getSourcePath('');
    }

    public function testGetSourcePathWithEncodedEntities(): void
    {
        $this->assertEquals('an image.jpg', $this->server->getSourcePath('an%20image.jpg'));
    }

    public function testSourceFileExists(): void
    {
        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->sourceFileExists('image.jpg'));
    }

    public function testSetBaseUrl(): void
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('img', $this->server->getBaseUrl());
    }

    public function testGetBaseUrl(): void
    {
        $this->assertEquals('', $this->server->getBaseUrl());
    }

    public function testSetCache(): void
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class));
        $this->assertInstanceOf(FilesystemOperator::class, $this->server->getCache());
    }

    public function testGetCache(): void
    {
        $this->assertInstanceOf(FilesystemOperator::class, $this->server->getCache());
    }

    public function testSetCachePathPrefix(): void
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img', $this->server->getCachePathPrefix());
    }

    public function testGetCachePathPrefix(): void
    {
        $this->assertEquals('', $this->server->getCachePathPrefix());
    }

    public function testSetInvalidTempDir(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->server->setTempDir('/invalid/path');
    }

    public function testSetGetTempDir(): void
    {
        $this->server->setTempDir(__DIR__);
        $this->assertSame(__DIR__.DIRECTORY_SEPARATOR, $this->server->getTempDir());
    }

    public function testSetCachePathCallable(): void
    {
        $this->server->setCachePathCallable(null);
        $this->assertEquals(null, $this->server->getCachePathCallable());
    }

    public function testGetCachePathCallable(): void
    {
        $this->assertEquals(null, $this->server->getCachePathCallable());
    }

    public function testCachePathCallableIsCalledOnGetCachePath(): void
    {
        $expected = 'TEST';
        $callable = function () use ($expected) {
            return $expected;
        };

        $this->server->setCachePathCallable($callable);

        self::assertEquals($expected, $this->server->getCachePath(''));
    }

    public function testSetCachePathCallableIsBoundClosure(): void
    {
        $server = $this->server;
        $phpUnit = $this;

        $this->server->setCachePathCallable(function () use ($server, $phpUnit) {
            $phpUnit::assertEquals($server, $this);

            return '';
        });

        $this->server->getCachePath('');
    }

    public function testSetCachePathCallableArgumentsAreSameAsGetCachePath(): void
    {
        $phpUnit = $this;
        $pathArgument = 'TEST';
        $optionsArgument = [
            'TEST' => 'TEST',
        ];
        $this->server->setCachePathCallable(function () use ($optionsArgument, $pathArgument, $phpUnit) {
            $arguments = func_get_args();
            $phpUnit::assertCount(2, $arguments);
            $phpUnit::assertEquals($arguments[0], $pathArgument);
            $phpUnit::assertEquals($arguments[1], $optionsArgument);

            return '';
        });

        $this->server->getCachePath($pathArgument, $optionsArgument);
    }

    public function testSetGroupCacheInFolders(): void
    {
        $this->server->setGroupCacheInFolders(false);

        $this->assertFalse($this->server->getGroupCacheInFolders());
    }

    public function testGetGroupCacheInFolders(): void
    {
        $this->assertTrue($this->server->getGroupCacheInFolders());
    }

    public function testSetCacheWithFileExtensions(): void
    {
        $this->server->setCacheWithFileExtensions(true);

        $this->assertTrue($this->server->getCacheWithFileExtensions());
    }

    public function testGetCacheWithFileExtensions(): void
    {
        $this->assertFalse($this->server->getCacheWithFileExtensions());
    }

    public function testGetCachePath(): void
    {
        $this->assertEquals(
            'image.jpg/382a458ecb704818',
            $this->server->getCachePath('image.jpg', ['w' => '100'])
        );
    }

    public function testGetCachePathWithNoFolderGrouping(): void
    {
        $this->server->setGroupCacheInFolders(false);

        $this->assertEquals(
            '382a458ecb704818',
            $this->server->getCachePath('image.jpg', ['w' => '100'])
        );
    }

    public function testGetCachePathWithPrefix(): void
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img/image.jpg/a2c14b0b5cf0e5a5', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithSourcePrefix(): void
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('image.jpg/a2c14b0b5cf0e5a5', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtension(): void
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->assertEquals('image.jpg/a2c14b0b5cf0e5a5.jpg', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtensionAndFmParam(): void
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->assertEquals('image.jpg/1521d6d426c257b2.gif', $this->server->getCachePath('image.jpg', ['fm' => 'gif']));
    }

    public function testGetCachePathWithExtensionAndPjpgFmParam(): void
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->assertEquals('image.jpg/58b79a7735b61b0d.jpg', $this->server->getCachePath('image.jpg', ['fm' => 'pjpg']));
    }

    public function testGetCachePathWithExtensionAndFmFromDefaults(): void
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->server->setDefaults(['fm' => 'gif']);
        $this->assertEquals('image.jpg/1521d6d426c257b2.gif', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtensionAndPjpgFmFromDefaults(): void
    {
        $this->server->setCacheWithFileExtensions(true);
        $this->server->setDefaults(['fm' => 'pjpg']);
        $this->assertEquals('image.jpg/58b79a7735b61b0d.jpg', $this->server->getCachePath('image.jpg', []));
    }

    public function testGetCachePathWithExtensionAndFmFromPreset(): void
    {
        $this->server->setCacheWithFileExtensions(true);

        $this->server->setPresets(['gif' => [
            'fm' => 'gif',
        ]]);

        $this->assertEquals('image.jpg/1521d6d426c257b2.gif', $this->server->getCachePath('image.jpg', ['p' => 'gif']));
    }

    public function testGetCachePathWithExtensionAndPjpgFmFromPreset(): void
    {
        $this->server->setCacheWithFileExtensions(true);

        $this->server->setPresets(['pjpg' => [
            'fm' => 'pjpg',
        ]]);

        $this->assertEquals('image.jpg/58b79a7735b61b0d.jpg', $this->server->getCachePath('image.jpg', ['p' => 'pjpg']));
    }

    public function testCacheFileExists(): void
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->with('image.jpg/a2c14b0b5cf0e5a5')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->cacheFileExists('image.jpg', []));
    }

    public function testDeleteCache(): void
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('deleteDirectory')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->deleteCache('image.jpg', []));
    }

    public function testDeleteCacheWithGroupCacheInFoldersDisabled(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Deleting cached image manipulations is not possible when grouping cache into folders is disabled.');

        $this->server->setGroupCacheInFolders(false);

        $this->server->deleteCache('image.jpg', []);
    }

    public function testSetApi(): void
    {
        $api = \Mockery::mock(ApiInterface::class);
        $this->server->setApi($api);
        $this->assertInstanceOf(ApiInterface::class, $this->server->getApi());
    }

    public function testGetApi(): void
    {
        $this->assertInstanceOf(ApiInterface::class, $this->server->getApi());
    }

    public function testSetDefaults(): void
    {
        $defaults = [
            'fm' => 'jpg',
        ];

        $this->server->setDefaults($defaults);

        $this->assertSame($defaults, $this->server->getDefaults());
    }

    public function testGetDefaults(): void
    {
        $this->testSetDefaults();
    }

    public function testSetPresets(): void
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

    public function testGetPresets(): void
    {
        $this->testSetPresets();
    }

    public function testGetAllParams(): void
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
            'invalid' => '1',
        ]);

        $this->assertSame([
            'fm' => 'jpg',
            'w' => '100',
            'h' => '200',
            'fit' => 'crop',
            'p' => 'small',
        ], $all_params);
    }

    public function testSetResponseFactory(): void
    {
        $this->server->setResponseFactory(\Mockery::mock(ResponseFactoryInterface::class));

        $this->assertInstanceOf(
            ResponseFactoryInterface::class,
            $this->server->getResponseFactory()
        );
    }

    public function testGetResponseFactory(): void
    {
        $this->testSetResponseFactory();
    }

    public function testGetImageResponse(): void
    {
        $this->server->setResponseFactory(\Mockery::mock(ResponseFactoryInterface::class, function ($mock) {
            $mock->shouldReceive('create')->andReturn(\Mockery::mock(ResponseInterface::class));
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true);
        }));

        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->server->getImageResponse('image.jpg', [])
        );
    }

    public function testGetImageResponseWithoutResponseFactory(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to get image response, no response factory defined.');

        $this->server->getImageResponse('image.jpg', []);
    }

    public function testGetImageAsBase64(): void
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true);
            $mock->shouldReceive('mimeType')->andReturn('image/jpeg');
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'data:image/jpeg;base64,Y29udGVudA==',
            $this->server->getImageAsBase64('image.jpg', [])
        );
    }

    public function testGetImageAsBase64WithUnreadableSource()
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Could not read the image `image.jpg/a2c14b0b5cf0e5a5`.');

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true);
            $mock->shouldReceive('mimeType')->andReturn('image/jpeg');
            $mock->shouldReceive('read')->andThrow('League\Flysystem\UnableToReadFile')->once();
        }));

        $this->server->getImageAsBase64('image.jpg', []);
    }

    #[RunInSeparateProcess]
    public function testOutputImage()
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true);
            $mock->shouldReceive('mimeType')->andReturn('image/jpeg');
            $mock->shouldReceive('fileSize')->andReturn(0);

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
        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(false)->once();
            $mock->shouldReceive('write')->withArgs(['image.jpg/a2c14b0b5cf0e5a5', 'content'])->once();
        }));

        $this->server->setApi(\Mockery::mock(ApiInterface::class, function ($mock) {
            $mock->shouldReceive('run')->withArgs(['content', []])->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/a2c14b0b5cf0e5a5',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromSourceWithCustomTmpDir()
    {
        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(false)->once();
            $mock->shouldReceive('write')->with('image.jpg/a2c14b0b5cf0e5a5', 'content')->once();
        }));

        $this->server->setTempDir(__DIR__);
        $this->server->setApi(\Mockery::mock(ApiInterface::class, function ($mock) {
            $mock->shouldReceive('run')->with('content', [])->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/a2c14b0b5cf0e5a5',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromCache()
    {
        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true);
        }));

        $this->assertEquals(
            'image.jpg/a2c14b0b5cf0e5a5',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromSourceThatDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('Could not find the image `image.jpg`.');

        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(false)->once();
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(false)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnreadableSource(): void
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Could not read the image `image.jpg`.');

        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true)->once();
            $mock->shouldReceive('read')->andThrow(UnableToReadFile::class)->once();
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andThrow(UnableToCheckFileExistence::class)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnwritableCache(): void
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Could not write the image `image.jpg/a2c14b0b5cf0e5a5`.');

        $this->server->setSource(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(\Mockery::mock(FilesystemOperator::class, function ($mock) {
            $mock->shouldReceive('fileExists')->andThrow(UnableToCheckFileExistence::class)->once();
            $mock->shouldReceive('write')->andThrow(UnableToWriteFile::class)->once();
        }));

        $this->server->setApi(\Mockery::mock(ApiInterface::class, function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }
}
