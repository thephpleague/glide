<?php

namespace League\Glide;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testConsole()
    {
        exec('php ' . __DIR__ . '/../bin/glide-console', $output);

        $searchStrings = [
            'help',
            'list',
            'generate-sign-key',
            'Generate a key for signing image URLs'
        ];

        $buffer = implode(PHP_EOL, $output);

        foreach ($searchStrings as $searchString) {
            $this->assertContains($searchString, $buffer);
        }
    }
}
