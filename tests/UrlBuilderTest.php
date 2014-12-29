<?php

namespace Glide;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $this->builder = new UrlBuilder();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\UrlBuilder', $this->builder);
    }

    public function testSetBaseUrl()
    {
        $this->builder->setBaseUrl('http://example.com');
        $this->assertEquals('http://example.com', $this->builder->getBaseUrl());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('', $this->builder->getBaseUrl());
    }

    public function testSetSignKey()
    {
        $this->builder->setSignKey('example');
        $this->assertEquals('example', $this->builder->getSignKey());
    }

    public function testGetSignKey()
    {
        $this->assertEquals(null, $this->builder->getSignKey());
    }

    public function testGetUrl()
    {
        $this->assertEquals('/image.jpg?w=100', $this->builder->getUrl('image.jpg', ['w' => '100']));
    }

    public function testGetUrlWithToken()
    {
        $this->builder->setSignKey('example');
        $this->assertEquals('/image.jpg?w=100&token=9978a40f1fc75fa64ac92ea9baf16ff3', $this->builder->getUrl('image.jpg', ['w' => '100']));
    }

    public function testGetToken()
    {
        $this->builder->setSignKey('example');
        $this->assertEquals('9978a40f1fc75fa64ac92ea9baf16ff3', $this->builder->getToken('image.jpg', ['w' => '100']));
    }
}
