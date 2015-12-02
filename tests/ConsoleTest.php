<?php

namespace League\Glide;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testConsole()
    {
        exec('php ' . __DIR__ . '/../bin/console', $output);

        $searchStrings = [
            'help',
            'list',
            'sign-key-generator',
            'Generate a key for signing image URLs'
        ];

        $buffer = implode(PHP_EOL, $output);

        foreach ($searchStrings as $searchString) {
            $this->assertContains($searchString, $buffer);
        }
    }
}
