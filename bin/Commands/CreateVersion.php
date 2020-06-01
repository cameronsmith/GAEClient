<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use UKCASmith\GAEClient\Services\Deploy;
use UKCASmith\GAEClient\Utils\DeployFile;

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

    /**
     * Configure bin script.
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates a new app engine version of an application.')
            ->setHelp('This command allows you to create an app engine application version going directly through GAE.');

        $this->addArgument('deployment type', InputArgument::REQUIRED, 'deployment type');
        $this->addOption(
            'label',
            null,
            InputOption::VALUE_REQUIRED,
            'custom label you want to provide.'
        );
    }

    /**
     * Execute.
     *
     * @param InputInterface $obj_input
     * @param OutputInterface $obj_output
     * @return int
     */
    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $this->obj_io = new SymfonyStyle($obj_input, $obj_output);
        $this->obj_io->title('Creating GAE Version');

        $str_environment = $obj_input->getArgument('deployment type');
        $str_label = $obj_input->getOption('label');

        try {
            $arr_deploy_json = DeployFile::getDeploySettings();
            $str_default_label = $arr_deploy_json[$str_environment]['version'];
            $str_deployment_label = (empty($str_label) ? $str_default_label : $str_label);

            $obj_question = new ConfirmationQuestion(
                'Please confirm you wish to deploy to ' . $str_environment . ' ' . $str_deployment_label,
                true
            );

            if (!$this->obj_io->askQuestion($obj_question)) {
                $this->obj_io->warning('Stopping deployment based on user input.');
                return 1;
            }

            $obj_deploy = new Deploy;
            $obj_generator = $obj_deploy
                ->setCustomLabel($str_deployment_label)
                ->process($arr_deploy_json, $str_environment);

            foreach ($obj_generator as $int_status) {
                $this->writeStatus($int_status);
            }
            $arr_response = $obj_generator->getReturn();

            if (isset($arr_response['error'])) {
                $arr_error = $arr_response['error'];
                $this->obj_io->error('Failed to create version: ' . $arr_response['name']);
                $this->obj_io->error('Code (' . $arr_error['code'] . '): ' . $arr_error['message']);
                return 1;
            }

            $str_link = $arr_response['response']['versionUrl'];

            $this->obj_io->newLine(2);
            $this->obj_io->writeln('<info>The application has been successfully deployed.</info>');
            $this->obj_io->writeln(
                'You can visit the new application at <href=' . $str_link . '>' . $str_link . '</>'
            );


        } catch (\Exception $obj_exception) {
            $this->obj_io->error(get_class($obj_exception) . ':' . $obj_exception->getMessage());
            return 1;
        }

        return 0;
    }

    protected function writeStatus($int_status)
    {
        switch ($int_status) {
            case Deploy::STEPS_COMPRESS:
                $this->obj_io->write('Creating source file: ');
                break;
            case Deploy::STEPS_UPLOAD:
                $this->obj_io->write('Uploading source file: ');
                break;
            case Deploy::STEPS_DEPLOY:
                $this->obj_io->write('Deploying: ');
                break;
            case Deploy::STEPS_CLEANUP:
                $this->obj_io->write('Cleaning up: ');
                break;
            case Deploy::STEPS_CONFIRM:
                $this->obj_io->write('Confirming: ');
                break;
            case Deploy::STEPS_SUCCESS:
                $this->obj_io->writeln('<info>OK</info>');
                break;
            case Deploy::STEPS_FAILED:
                $this->obj_io->writeln('<error>FAILED</error>');
                break;
        }
    }
}