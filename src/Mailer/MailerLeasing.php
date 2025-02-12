<?php

namespace AcMarche\Leasing\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class MailerLeasing
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendLeasing(array $data, string $filePath, string $mailTo): TemplatedEmail
    {
        $message = new TemplatedEmail();
        $message
            ->from('no-reply@acmarche.be')
            ->subject('Une demande de leasing de '.$data['prenom'].' '.$data['nom'])
            ->to($mailTo)
            ->cc($data['email'])
            ->htmlTemplate('@AcMarcheLeasing/leasing/mail.html.twig')
            ->context([
                'data' => $data,
            ]);

        $message->attachFromPath($filePath);

        return $message;
    }

    /**
     * @param TemplatedEmail $email
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }

}
