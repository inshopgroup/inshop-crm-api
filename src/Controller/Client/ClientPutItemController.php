<?php

namespace App\Controller\Client;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\User\BaseUserController;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ClientPutItemController
 * @package App\Controller\User
 */
class ClientPutItemController extends BaseUserController
{
    /**
     * @param UserInterface $data
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function __invoke(
        UserInterface $data,
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em

    ): JsonResponse {
        $params = json_decode($request->getContent());

        if ($data instanceof Client) {
            if (isset($params->name)) {
                $data->setName($params->name);
            }

            if (isset($params->plainPassword)) {
                $data->setPlainPassword($params->plainPassword);
            }

            $this->encodePassword($data);
            $validator->validate($data);

            $em->flush();
        }

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
