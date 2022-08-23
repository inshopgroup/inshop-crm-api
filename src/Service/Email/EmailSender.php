<?php

namespace App\Service\Email;

use App\Entity\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailSender
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     * @param Environment $twig
     */
    public function __construct(
        MailerInterface $mailer,
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
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(Client $user, string $subject, string $templateName, array $params): void
    {
        $this->translator->setLocale('en');
        $message = (new Email())
            ->subject($this->translator->trans($subject))
            ->from('noreply@test.pl')
            ->to($user->getUsername())
            ->html(
                $this->twig->render(
                    'emails/' . $templateName,
                    $params
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
