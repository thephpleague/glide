<?php

namespace League\Glide\Factories;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $urlBuilder = UrlBuilder::create('/img');

        $this->assertInstanceOf('League\Glide\UrlBuilder', $urlBuilder);
        $this->assertEquals('/img/image.jpg', $urlBuilder->getUrl('image.jpg'));
    }

    public function testCreateWithSignKey()
    {
        $urlBuilder = UrlBuilder::create('/img', 'example');

        $this->assertEquals(
            '/img/image.jpg?s=2aed6cf637d60951a66200eda3f5e568',
            $urlBuilder->getUrl('image.jpg')
        );
    }
}
