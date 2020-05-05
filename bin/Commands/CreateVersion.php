<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UKCASmith\GAEClient\Client;
use UKCASmith\GAEClient\Requests\Version;
use UKCASmith\GAEClient\Compress\Files\IgnoreFolderDots;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use UKCASmith\GAEClient\Compress\Factory;
use Google\Cloud\Storage\StorageClient;
use UKCASmith\GAEClient\Services\Deploy;

class CreateVersion extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'google:create-version';

    /**
     * @var SymfonyStyle
     */
    protected $obj_io;

    protected function configure()
    {
        $this
            ->setDescription('Creates a new app engine version of an application.')
            ->setHelp('This command allows you to create an app engine application version.');

        $this->addArgument('deployment type', InputArgument::REQUIRED, 'deployment type');
        $this->addArgument('label', InputArgument::REQUIRED, 'version label');
    }

    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $this->obj_io = new SymfonyStyle($obj_input, $obj_output);
        $this->obj_io->title('Creating GAE Version');

        $arr_deploy_json = $this->getDeployFile();
        $str_environment = $obj_input->getArgument('deployment type');

        try {
            $obj_question = new ConfirmationQuestion(
                'Please confirm you wish to deploy to ' . $str_environment,
                true
            );

            if (!$this->obj_io->askQuestion($obj_question)) {
                $this->obj_io->warning('Stopping deployment based on user input.');
                return 1;
            }

            $obj_deploy = new Deploy;
            $obj_generator = $obj_deploy->process($arr_deploy_json, $str_environment, true);
            foreach($obj_generator as $int_result) {
                switch($int_result) {
                    case Deploy::STEPS_COMPRESS:
                        $this->obj_io->write('Creating source file: ');
                        break;
                    case Deploy::STEPS_UPLOAD:
                        $this->obj_io->write('Uploading source file: ');
                        break;
                    case Deploy::STEPS_DEPLOY:
                        $this->obj_io->write('Deploying: ');
                        break;
                    case Deploy::STEPS_SUCCESS:
                        $this->obj_io->writeln('<info>OK</info>');
                        break;
                }
            }
        } catch (\Exception $obj_exception) {
            $this->obj_io->error($obj_exception->getMessage());
            return 1;
        }

        return 0;
    }

    protected function getDeployFile() {
        $str_deploy_file = getcwd() . DIRECTORY_SEPARATOR . 'deploy.json';

        if (!file_exists($str_deploy_file)) {
            $this->obj_io->error([
                'Cannot locate deploy.json file within your current working directory.',
                'For more information please refer to https://github.com/uk-casmith/GAEClient to learn how to creating deploy.json files.'
            ]);
            exit(1);
        }

        return json_decode(file_get_contents($str_deploy_file), true);
    }
}