<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Initialize extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:init';

    /**
     * @var SymfonyStyle
     */
    protected $obj_io;


    protected function configure()
    {
        $this
            ->setDescription('Creates a new app deploy.json.')
            ->setHelp('This command creates a default deploy.json file that can be customized at a later date.');
    }

    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $this->obj_io = new SymfonyStyle($obj_input, $obj_output);
        $this->obj_io->title('Initializing');

        if ($this->doesDeployFileExists()) {
            $this->obj_io->error([
                'Cannot create a new deploy.json file within your current working directory one already exists!'
            ]);
        }

        return 0;
    }

    protected function doesDeployFileExists() {
        return file_exists(getcwd() . DIRECTORY_SEPARATOR . 'deploy.json');
    }
}