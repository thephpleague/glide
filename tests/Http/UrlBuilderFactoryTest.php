<?php

namespace League\Glide\Http;

class UrlBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $urlBuilder = UrlBuilderFactory::create('/img');

        $this->assertInstanceOf('League\Glide\Http\UrlBuilder', $urlBuilder);
        $this->assertEquals('/img/image.jpg', $urlBuilder->getUrl('image.jpg'));
    }

    public function testCreateWithSignKey()
    {
        $urlBuilder = UrlBuilderFactory::create('/img', 'example');

        $this->assertEquals(
            '/img/image.jpg?s=2aed6cf637d60951a66200eda3f5e568',
            $urlBuilder->getUrl('image.jpg')
        );
    }
}
