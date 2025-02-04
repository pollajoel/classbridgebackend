<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;


final class User extends JWTUser implements JWTUserInterface{

    /** @var string */
    private $email;

    /**
     * {@inheritdoc}
     */
    public static function createFromPayload($username, array $payload): JWTUserInterface
    {
        $userRoles = $payload["roles"] ? array_values($payload["roles"]) : [];
        return new static($username, array_unique($userRoles));
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

}