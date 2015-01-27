<?php

namespace League\Glide\Http;

use Mockery;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $response;
    private $cache;

    public function setUp()
    {
        $this->cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $file = tmpfile();
            fwrite($file, 'content');
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('getTimestamp')->andReturn(time());
            $mock->shouldReceive('readStream')->andReturn($file);
        });

        $this->response = new ResponseFactory($this->cache, RequestFactory::create('image.jpg'), 'image.jpg');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Http\ResponseFactory', $this->response);
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $this->response->getResponse('image.jpg'));
    }

    public function testSetHeaders()
    {
        $response = $this->response->setHeaders(new StreamedResponse(), 'image.jpg');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertEquals('0', $response->headers->get('Content-Length'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', strtotime('+1 years')).' GMT', $response->headers->get('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
    }

    public function setContent()
    {
        $response = $this->response->setHeaders(new StreamedResponse(), 'image.jpg');

        ob_start();
        $response->send();
        $content = ob_get_clean();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('content', $content);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            ResponseFactory::create($this->cache, RequestFactory::create('image.jpg'), 'image.jpg')
        );
    }
}
