<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UKCASmith\GAEClient\Requests\Auth;

class GetAuthHeader extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'google:auth-header';

    /**
     * @var SymfonyStyle
     */
    protected $obj_io;


    protected function configure()
    {
        $this
            ->setDescription('Gets the Authorization header for a google request which is useful for postman.')
            ->setHelp('
            This command allows you to the authorization header used on google requested. This 
            can be useful when you want to create a request within postman and require an authorization token.');
    }

    protected function execute(InputInterface $obj_input, OutputInterface $obj_output)
    {
        $this->obj_io = new SymfonyStyle($obj_input, $obj_output);
        $this->obj_io->title('Gettting Google OAuth Header');


        $obj_auth = new Auth;
        $this->obj_io->writeln('Authorization: <info>' . $obj_auth->execute() . '</info>');
        $this->obj_io->newLine(2);
        $this->obj_io->writeln('Please note this authorization token will only last a limited period of time.');

        return 0;
    }
}