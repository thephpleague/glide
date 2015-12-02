<?php

namespace League\Glide\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SignKeyGeneratorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sign-key-generator')
            ->setDescription('Generate a key for signing image URLs')
            ->addOption(
                'entropy',
                'e',
                InputOption::VALUE_OPTIONAL,
                'How many bytes of entropy do you want in your key? (defaults to 32 bytes or 256 bits)',
                32
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'What format would do you want your key provided? (hex or base64, defaults to base64)',
                'base64'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entropy = $input->getOption('entropy');
        $format = $input->getOption('format');

        $formatters = [
            'base64' => 'base64_encode',
            'hex' => 'bin2hex',
        ];

        if (!isset($formatters[$format])) {
            throw new \InvalidArgumentException('Unrecognized format: ' . $format);
        }

        $data = random_bytes($entropy);
        $result = $formatters[$format]($data);

        $output->writeln($result);
    }
}
