<?php

namespace League\Glide\Command;

class SignKeyGeneratorCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $command = new SignKeyGeneratorCommand();
        $this->assertEquals('sign-key-generator', $command->getName());
        $this->assertEquals('Generate a key for signing image URLs', $command->getDescription());
    }

    public function testExecute()
    {
    }
}
