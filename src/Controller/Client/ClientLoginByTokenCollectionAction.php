<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientLoginByTokenCollectionAction
{
    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    private ClientRepository $clientRepository;

    private EntityManagerInterface $em;

    public function __construct(
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        ClientRepository $clientRepository,
        EntityManagerInterface $em
    ) {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->clientRepository = $clientRepository;
        $this->em = $em;
    }

    public function __invoke(Request $request): JWTAuthenticationSuccessResponse
    {
        /** @var Client $client */
        $client = $this->clientRepository->findByToken($request->get('token'));

        if ($client && (clone $client->getTokenCreatedAt())->modify('+1 day') >= new DateTime('now')) {
            $client->setIsActive(true);
            $this->em->flush();

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($client);
        }

        throw new NotFoundHttpException();
    }
}
