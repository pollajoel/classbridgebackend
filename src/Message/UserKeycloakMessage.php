<?php

namespace App\Message;

class UserKeycloakMessage
{
    private string $username;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $password;
    private bool   $emailVerified;
    private bool   $enabled;

    public function __construct(
        string $username, 
        string $email, 
        string $firstName,
        string $lastName, 
        string $password, 
        bool   $emailVerified, 
        bool   $enabled
    )
    {
        $this->username = $username;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->emailVerified = $emailVerified;
        $this->enabled = $enabled;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }
}
