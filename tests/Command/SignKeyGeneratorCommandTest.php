<?php

namespace League\Glide\Command;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

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
        $this->assertBase64($this->getCommandOutput(), 32);
        $this->assertHex($this->getCommandOutput(null, 'hex'), 32);

        $lengths = [1, 4, 8, 16, 32, 256, 1024];

        foreach ($lengths as $length) {
            $this->assertBase64($this->getCommandOutput($length, 'base64'), $length);
            $this->assertHex($this->getCommandOutput($length, 'hex'), $length);
        }
    }

    private function getCommandOutput($entropy = null, $format = null)
    {
        $command = new SignKeyGeneratorCommand();

        $argv = '';

        if (null !== $entropy) {
            $argv .= ' --entropy=' . $entropy;
        }

        if (null !== $format) {
            $argv .= ' --format=' . $format;
        }

        $input = new StringInput($argv);
        $output = new BufferedOutput;

        $command->run($input, $output);

        $buffer = $output->fetch();

        // Check that a trailing new line is printed then remove it
        $this->assertStringEndsWith(PHP_EOL, $buffer);
        return substr($buffer, 0, -1);
    }

    private function assertBase64($string, $rawLength = null)
    {
        $decoded = base64_decode($string, true);

        if (false === $decoded) {
            $this->fail('Failed to decode ' . $string . ' as base64');
        }

        if (null !== $rawLength) {
            $this->assertEquals($rawLength, strlen($decoded));
        }
    }

    private function assertHex($string, $rawLength = null)
    {
        $decoded = hex2bin($string);

        if (false === $decoded) {
            $this->fail('Failed to decode ' . $string . ' as hex');
        }

        if (null !== $rawLength) {
            $this->assertEquals($rawLength, strlen($decoded));
        }
    }
}
