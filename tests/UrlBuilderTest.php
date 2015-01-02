<?php

namespace Glide;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\UrlBuilder', new UrlBuilder());
    }

    public function testGetUrl()
    {
        $urlBuilder = new UrlBuilder('http://example.com');

        $this->assertEquals(
            'http://example.com/image.jpg?w=100',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }

    public function testGetUrlWithToken()
    {
        $urlBuilder = new UrlBuilder('http://example.com', 'example');

        $this->assertEquals(
            'http://example.com/image.jpg?w=100&token=9978a40f1fc75fa64ac92ea9baf16ff3',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }
}
