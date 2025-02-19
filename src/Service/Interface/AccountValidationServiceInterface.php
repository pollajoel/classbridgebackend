<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\AccountValidation;
use App\Entity\User;

interface AccountValidationServiceInterface
{

    public function findAccountValidationByCode(string $validationCode): AccountValidation;
    public function attachValidattionToUser(User $user): AccountValidation;
    public function generateValidationCode(): String;
    public function validateAccount(string $token): User | null;
    public function validationHasNotExpired(string $token): bool;
}
