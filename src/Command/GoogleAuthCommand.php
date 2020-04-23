<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GoogleClient;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GoogleAuthCommand
 * @package App\Command
 */
class GoogleAuthCommand extends Command
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var GoogleClient
     */
    protected $googleClient;

    /**
     * GoogleAuthCommand constructor.
     * @param UserRepository $userRepository
     * @param GoogleClient $googleClient
     */
    public function __construct(UserRepository $userRepository, GoogleClient $googleClient)
    {
        $this->userRepository = $userRepository;
        $this->googleClient = $googleClient;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('google:auth')
            ->setDescription('Authorize user to sync event to google calendar')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $input->getArgument('username')]);

        if ($user) {
            $io = new SymfonyStyle($input, $output);
            $io->success(sprintf('Authorizing user "%s"', $user->getUsername()));

            $this->googleClient->init($user);
            $this->googleClient->auth($user);
        }
    }
}
