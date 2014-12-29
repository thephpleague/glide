<?php

namespace Glide;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $server = Factory::server([
            'source' => 'source',
            'cache' => 'cache',
        ]);

        $this->assertInstanceOf('Glide\Server', $server);
    }
}
