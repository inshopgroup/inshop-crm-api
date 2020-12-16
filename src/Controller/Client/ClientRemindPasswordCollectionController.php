<?php

namespace App\Controller\Client;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\User\BaseUserController;
use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Swift_Mailer $mailer
     * @param ParameterBagInterface $params
     * @param ValidatorInterface $validator
     * @param ClientRepository $clientRepository
     * @return Client
     * @throws Exception
     */
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        Swift_Mailer $mailer,
        ParameterBagInterface $params,
        ValidatorInterface $validator,
        ClientRepository $clientRepository
    ): Client {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $client = null;

        if (isset($data['username'])) {
            /** @var Client $client */
            $client = $clientRepository->getClientByEmail($data['username']);
        }

        if (!$client) {
            throw new ValidationException(
                new ConstraintViolationList([
                    new ConstraintViolation('User not found', '', [], '', 'username', 'invalid'),
                ])
            );
        }

        $client->setToken(bin2hex(random_bytes(32)));
        $client->setTokenCreatedAt(new DateTime());

        $client = $this->encodePassword($client);

        $validator->validate($client);
        $em->flush();

        try {
            $message = (new Swift_Message())
                ->setSubject('Remind password')
                ->setFrom('noreply@inshopcrm.com')
                ->setTo($client->getUsername())
                ->setBody(
                    $this->renderView(
                        'emails/signup.html.twig',
                        [
                            'user' => $client,
                            'url' => sprintf('%s%s%s', $params->get('client_url'), '/token/login/', $client->getToken()),
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);
        } catch (Exception $e) {}

        return $client;
    }
}
