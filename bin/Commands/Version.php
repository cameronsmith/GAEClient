<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class Version extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:version';

    protected function configure()
    {
        $this
            ->setDescription('Gets the current version of the application.')
            ->setHelp('Gets the current version of the application.');
    }

    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $obj_output->writeln([
            'Creating App Engine',
            '============',
            '',
        ]);

        $obj_output->writeln('Version: <info>'. VERSION .'</info>');

        return 0;
    }
}