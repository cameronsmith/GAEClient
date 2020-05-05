<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class BuildImage extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:build-image';

    protected function configure()
    {
        $this
            ->setDescription('Builds and uploads a version of your application to the GAE Manager.')
            ->setHelp('This command allows you to create an app engine application version.');

        $this->addArgument('label', InputArgument::REQUIRED, 'Application label');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Creating App Engine',
            '============',
            '',
        ]);
    }
}