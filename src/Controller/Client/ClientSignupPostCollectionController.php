<?php

namespace App\Controller\Client;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\User\BaseUserController;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClientSignupPostCollectionController
 * @package App\Controller\User
 */
class ClientSignupPostCollectionController extends BaseUserController
{
    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param \Swift_Mailer $mailer
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $em
     * @return Client
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        ValidatorInterface $validator,
        \Swift_Mailer $mailer,
        ParameterBagInterface $params,
        EntityManagerInterface $em
    ): Client {
        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setUsername($data['username']);
        $client->setName($data['name']);
        $client->setPlainPassword($data['plainPassword']);
//        $client->setIsActive(false);
        $client->setToken(\bin2hex(\random_bytes(32)));
        $client->setTokenCreatedAt(new \DateTime());

        /** @var Client $client */
        $client = $this->encodePassword($client);

        $validator->validate($client, ['groups' => 'signup']);
        $em->flush();

        try {
            $message = (new \Swift_Message())
                ->setSubject('Confirm registration')
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
        } catch (\Exception $e) {}

        return $client;
    }
}
