<?php
namespace App\Service;

use App\Entity\AccountValidation;
use App\Entity\User;
use App\Message\EmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\MessageBusInterface;

 class MailerService {
    public function __construct(
        private readonly MailerInterface $mailer,
        private MessageBusInterface $bus
    )
    {
        
    }
    public function sendValidationEmail(User $user, AccountValidation $validation): void
    {
        
        $email = new EmailMessage(
            $user,
            'no-reply@classbridge.com',
            'Your validation Code',
            [
                "validation" => $validation,
                "user"       => $user
            ]
        ); 
        
        $this->bus->dispatch($email);
    }
}