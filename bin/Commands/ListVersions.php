<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListVersions extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'google:list-versions';

    /**
     * @var SymfonyStyle
     */
    protected $obj_io;


    protected function configure()
    {
        $this
            ->setDescription('Lists all versions for your project.')
            ->setHelp('This command allows you to create an app engine application version.');

        $this->addArgument('label', InputArgument::REQUIRED, 'version label');

        $this->addOption(
            'file',
            null,
            InputArgument::OPTIONAL,
            'optionally specify the deploy.json file to use.');
    }

    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $this->obj_io = new SymfonyStyle($obj_input, $obj_output);
        $this->obj_io->title('Creating GAE Version');
        //$obj_io->section('Adding a User');

        $this->getDeployFile($obj_input->getOption('file'));

        return 0;
    }

    protected function getDeployFile($str_filename) {
        $str_deploy_file = (!empty($str_filename)) ? $str_filename : getcwd() . DIRECTORY_SEPARATOR . 'deploy.json';

        if (!file_exists($str_deploy_file)) {
            $this->obj_io->error([
                'Cannot locate deploy.json file within your current working directory.',
                'For more information please refer to https://github.com/uk-casmith/GAEClient to learn how to creating deploy.json files.'
            ]);
            exit(1);
        }
    }
}