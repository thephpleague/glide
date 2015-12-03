<?php

namespace League\Glide\Command;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class GenerateSignKeyCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandSetup()
    {
        $command = new GenerateSignKeyCommand();
        $this->assertEquals('generate-sign-key', $command->getName());
        $this->assertEquals('Generate a key for signing image URLs', $command->getDescription());
    }

    public function testCommandOutput()
    {
        $this->assertBase64($this->getCommandOutput(), 32);
        $this->assertHex($this->getCommandOutput(null, 'hex'), 32);

        $lengths = [1, 4, 8, 16, 32, 256, 1024];

        foreach ($lengths as $length) {
            $this->assertBase64($this->getCommandOutput($length, 'base64'), $length);
            $this->assertHex($this->getCommandOutput($length, 'hex'), $length);
        }
    }

    public function testInvalidFormat()
    {
        try {
            $this->getCommandOutput(null, 'invalid-format');
            $this->fail('Should have thrown InvalidArgumentException when provided with invalid format');
        } catch (\InvalidArgumentException $exception) {
            $this->assertEquals('Unrecognized format: invalid-format', $exception->getMessage());
        }
    }

    private function getCommandOutput($entropy = null, $format = null)
    {
        $command = new GenerateSignKeyCommand();

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
