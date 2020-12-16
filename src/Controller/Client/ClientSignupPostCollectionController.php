<?php

namespace App\Controller\Client;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\User\BaseUserController;
use App\Entity\Client;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use function bin2hex;
use function random_bytes;

/**
 * Class ClientSignupPostCollectionController
 * @package App\Controller\User
 */
class ClientSignupPostCollectionController extends BaseUserController
{
    /**
     * @param Client $data
     * @param ValidatorInterface $validator
     * @param Swift_Mailer $mailer
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $em
     * @return Client
     * @throws Exception
     */
    public function __invoke(
        Client $data,
        ValidatorInterface $validator,
        Swift_Mailer $mailer,
        ParameterBagInterface $params,
        EntityManagerInterface $em
    ): Client {
        $data->setToken(bin2hex(random_bytes(32)));
        $data->setTokenCreatedAt(new DateTime());

        /** @var Client $data */
        $data = $this->encodePassword($data);

        $validator->validate($data, ['groups' => 'signup']);
        $em->flush();

        try {
            $message = (new Swift_Message())
                ->setSubject('Confirm registration')
                ->setFrom('noreply@inshopcrm.com')
                ->setTo($data->getUsername())
                ->setBody(
                    $this->renderView(
                        'emails/signup.html.twig',
                        [
                            'user' => $data,
                            'url' => sprintf('%s%s%s', $params->get('client_url'), '/token/login/', $data->getToken()),
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);
        } catch (Exception $e) {}

        return $data;
    }
}
