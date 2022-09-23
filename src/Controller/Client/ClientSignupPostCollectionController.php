<?php

namespace App\Controller\Client;

use App\Controller\User\BaseUserController;
use App\Entity\Client;
use App\Service\Email\EmailSender;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function bin2hex;
use function random_bytes;

class ClientSignupPostCollectionController extends BaseUserController
{
    private EntityManagerInterface $em;

    private EmailSender $emailSender;

    private ParameterBagInterface $params;

    public function __construct(
        UserPasswordHasherInterface $encoder,
        EmailSender $emailSender,
        ParameterBagInterface $params
    ) {
        parent::__construct($encoder);
        $this->emailSender = $emailSender;
        $this->params = $params;
    }

    public function __invoke(Client $data): Client
    {
        $data->setToken(bin2hex(random_bytes(32)));
        $data->setTokenCreatedAt(new DateTime());
        $data->setIsActive(false);

        /** @var Client $client */
        $client = $this->encodePassword($data);

        $parameters = [
            'user' => $client,
            'url' => sprintf('%s%s%s', $this->params->get('client_url'), '/token/login/', $client->getToken()),
        ];

        $this->emailSender->sendEmail($client, 'confirm_registration', 'signup.html.twig', $parameters);

        return $client;
    }
}
