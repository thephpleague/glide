<?php

namespace Glide;

use Mockery;

class OutputTest extends \PHPUnit_Framework_TestCase
{
    private $output;

    public function setUp()
    {
        $cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $file = tmpfile();
            fwrite($file, 'content');
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn($file);
        });

        $this->output = new Output($cache);
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Output', $this->output);
    }

    public function testSetCache()
    {
        $this->output->setCache(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->output->getCache());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->output->getCache());
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        ob_start();
        $response = $this->output->output('image.jpg');
        $content = ob_get_clean();

        $this->assertEquals('content', $content);
        $this->assertNull($response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendHeaders()
    {
        $headers = $this->output->sendHeaders('image.jpg');

        $this->assertEquals('image/jpeg', $headers['Content-Type']);
        $this->assertEquals('0', $headers['Content-Length']);
        $this->assertEquals(gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT', $headers['Expires' ]);
        $this->assertEquals('public, max-age=31536000', $headers['Cache-Control']);
        $this->assertEquals('public', $headers['Pragma']);
    }

    public function testSendImage()
    {
        ob_start();
        $response = $this->output->sendImage('image.jpg');
        $content = ob_get_clean();

        $this->assertEquals('content', $content);
        $this->assertNull($response);
    }
}
