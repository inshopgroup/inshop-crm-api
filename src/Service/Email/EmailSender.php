<?php

namespace App\Service\Email;

use App\Entity\Client;
use Psr\Container\ContainerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailSender
{
    /**
     * @var Swift_Mailer
     */
    private Swift_Mailer $mailer;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * EmailSender constructor.
     * @param Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     * @param Environment $twig
     */
    public function __construct(
        Swift_Mailer $mailer,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * @param Client $user
     * @param string $subject
     * @param string $templateName
     * @param array $params
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendEmail(Client $user, string $subject, string $templateName, array $params): void
    {
        $this->translator->setLocale('en');
        $message = (new Swift_Message())
            ->setSubject($this->translator->trans($subject))
            ->setFrom('noreply@test.pl')
            ->setTo($user->getUsername())
            ->setBody(
                $this->twig->render(
                    'emails/' . $templateName,
                    $params
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
