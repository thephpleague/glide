<?php

namespace League\Glide\Http;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Http\UrlBuilder', new UrlBuilder());
    }

    public function testGetUrl()
    {
        $urlBuilder = new UrlBuilder('http://example.com');

        $this->assertEquals(
            'http://example.com/image.jpg?w=100',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetUrlWithNoDomain()
    {
        $urlBuilder = new UrlBuilder();

        $this->assertEquals(
            '/image.jpg?w=100',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetUrlWithDomainAndPort()
    {
        $urlBuilder = new UrlBuilder('http://localhost:8000');

        $this->assertEquals(
            'http://localhost:8000/image.jpg?w=100',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetUrlWithToken()
    {
        $urlBuilder = new UrlBuilder('http://example.com', new Signature('example'));

        $this->assertEquals(
            'http://example.com/image.jpg?w=100&s=ada01955c63b41fff1ea1f65522a0444',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetInvalidUrl()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Not a valid path.');

        $urlBuilder = new UrlBuilder(':80');
        $urlBuilder->getUrl('image.jpg');
    }
}
