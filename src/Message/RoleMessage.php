<?php

namespace App\Message;

class RoleMessage
{
    private array $roles;
    private $username;

    public function __construct(array $roles, string $username)
    {
        $this->roles = $roles;
        $this->username = $username;
    }

    public function getRoles(): array{
        return $this->roles;
    }

    public function getUsername(): string{
        return $this->username;
    }

}