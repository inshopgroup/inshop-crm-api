<?php

namespace App\Service\Email;

use App\Entity\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EmailSender
{
    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    private Environment $twig;

    public function __construct(
        MailerInterface $mailer,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->twig = $twig;
    }

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
