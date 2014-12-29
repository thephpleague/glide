<?php

namespace Glide;

use Mockery;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function tearDown()
    {
        Mockery::close();
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

    public function testGetResponse()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $this->output->getResponse('image.jpg'));
    }


    public function testSetHeaders()
    {
        $response = $this->output->setHeaders(new StreamedResponse(), 'image.jpg');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertEquals('0', $response->headers->get('Content-Length'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT', $response->headers->get('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
    }

    public function setContent()
    {
        $response = $this->output->setHeaders(new StreamedResponse(), 'image.jpg');

        ob_start();
        $response->send();
        $content = ob_get_clean();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('content', $content);
    }
}
