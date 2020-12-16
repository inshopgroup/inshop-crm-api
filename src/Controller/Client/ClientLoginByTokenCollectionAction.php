<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClientLoginByTokenCollectionAction
 * @package App\Controller\User
 */
class ClientLoginByTokenCollectionAction
{
    /**
     * @param Request $request
     * @param AuthenticationSuccessHandler $authenticationSuccessHandler
     * @param ClientRepository $clientRepository
     * @return JWTAuthenticationSuccessResponse
     * @throws NonUniqueResultException
     */
    public function __invoke(
        Request $request,
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        ClientRepository $clientRepository
    ): JWTAuthenticationSuccessResponse {
        /** @var Client $client */
        $client = $clientRepository->findByToken($request->get('token'));

        if (!$client) {
            throw new NotFoundHttpException();
        }

        return $authenticationSuccessHandler->handleAuthenticationSuccess($client);
    }
}
