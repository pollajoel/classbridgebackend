<?php

namespace App\Consumer;

use App\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler()]
class EmailMessageHandler {

    
    public function __construct(private MailerInterface $mailer){}

    public function __invoke(EmailMessage $emailMessage):void
    {
       
        $email = (new TemplatedEmail())
        ->from($emailMessage->getFrom())
        ->to(new Address( $emailMessage->getTo()->getEmail()) )
        ->subject($emailMessage->getSubject())
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->htmlTemplate("emails/validation.html.twig")
        ->locale('fr-FR')
        ->context($emailMessage->getContext());
        // Envoyer l'email
        $this->mailer->send($email);
        
    }


}