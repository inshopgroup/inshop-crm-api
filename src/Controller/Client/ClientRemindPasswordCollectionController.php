<?php

namespace App\Controller\Client;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Controller\User\BaseUserController;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\Email\EmailSender;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

use function bin2hex;
use function random_bytes;

/**
 * Class ClientRemindPasswordCollectionController
 * @package App\Controller\User
 */
class ClientRemindPasswordCollectionController extends BaseUserController
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $params;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @var EmailSender
     */
    private EmailSender $emailSender;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * ClientRemindPasswordCollectionController constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param ParameterBagInterface $params
     * @param ClientRepository $clientRepository
     * @param EmailSender $emailSender
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        ParameterBagInterface $params,
        ClientRepository $clientRepository,
        EmailSender $emailSender,
        EntityManagerInterface $em
    ) {
        parent::__construct($encoder);
        $this->params = $params;
        $this->clientRepository = $clientRepository;
        $this->emailSender = $emailSender;
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Client
     * @throws \JsonException
     * @throws Exception
     */
    public function __invoke(Request $request): Client
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $client = null;

        if (isset($data['username'])) {
            /** @var Client $client */
            $client = $this->clientRepository->getClientByEmail($data['username']);
        }

        if (!$client) {
            throw new ValidationException(
                new ConstraintViolationList(
                    [
                        new ConstraintViolation('User not found', '', [], '', 'username', 'invalid'),
                    ]
                )
            );
        }

        $client->setToken(bin2hex(random_bytes(32)));
        $client->setTokenCreatedAt(new DateTime());

        $this->em->flush();

        $parameters = [
            'user' => $client,
            'url' => sprintf(
                '%s%s%s',
                $this->params->get('client_url'),
                '/token/login/',
                $client->getToken()
            ),
        ];

        $this->emailSender->sendEmail($client, 'remind_password', 'remind.html.twig', $parameters);

        return $client;
    }
}
