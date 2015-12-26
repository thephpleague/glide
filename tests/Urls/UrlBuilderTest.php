<?php

namespace League\Glide\Urls;

use League\Glide\Signatures\Signature;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Urls\UrlBuilder', new UrlBuilder());
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

    public function testGetUrlWithProtocolRelativeDomain()
    {
        $urlBuilder = new UrlBuilder('//localhost:8000');

        $this->assertEquals(
            '//localhost:8000/image.jpg?w=100',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetUrlWithToken()
    {
        $urlBuilder = new UrlBuilder('http://example.com', new Signature('example-sign-key'));

        $this->assertEquals(
            'http://example.com/image.jpg?w=100&s=e1b69d4b79ecf33283128819fd008906',
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
