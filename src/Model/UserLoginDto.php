<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserLoginDto {

    public function __construct(
        #[Assert\NotBlank()]
        public string $username,
        #[Assert\NotBlank()]
        public string $password
    ){}
}