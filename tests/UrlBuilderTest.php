<?php

namespace League\Glide;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\UrlBuilder', new UrlBuilder());
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
        $urlBuilder = new UrlBuilder('http://example.com', new HttpSignature('example'));

        $this->assertEquals(
            'http://example.com/image.jpg?w=100&s=ada01955c63b41fff1ea1f65522a0444',
            $urlBuilder->getUrl('image.jpg', ['w' => '100'])
        );
    }
}
