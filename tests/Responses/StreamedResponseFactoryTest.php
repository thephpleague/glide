<?php

namespace League\Glide\Responses;

use League\Glide\Requests\RequestFactory;
use Mockery;

class StreamedResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $cache;

    private $request;

    private $factory;

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

        $this->request = RequestFactory::create('image.jpg');

        $this->factory = new StreamedResponseFactory();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Responses\StreamedResponseFactory', $this->factory);
    }

    public function testGetResponse()
    {
        $response = $this->factory->getResponse($this->request, $this->cache, '');

        ob_start();
        $response->send();
        $content = ob_get_clean();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertEquals('0', $response->headers->get('Content-Length'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', strtotime('+1 years')).' GMT', $response->headers->get('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
        $this->assertEquals('content', $content);
    }
}
