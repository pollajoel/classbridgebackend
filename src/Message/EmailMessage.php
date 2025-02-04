<?php

namespace App\Message;

use App\Entity\User;

class EmailMessage
{
    private User $to;
    private string $subject;
    private string $from;
    private array $context; // Contextual data to be included in the email body. This could be a list of upcoming events, a personalized welcome message, etc.


    public function __construct(User $to, string $from, string $subject, array $context)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->from = $from;
        $this->context = $context;
    }

    public function getTo(): User
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }


    public function getFrom():string{
        return $this->from;
    }

    public function getContext(): array
    {
        return $this->context;
    }


}
