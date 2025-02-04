<?php

namespace  App\Service;

use App\Entity\AccountValidation;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountValidationService
{

    const VALIDATION_CODE_PREFIX = 'ValidationCode#__';
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly MailerService $message
    ) {}


    public function findAccountValidationByCode(string $validationCode): AccountValidation
    {

        $validationEntityManager  = $this->entityManager->getRepository(AccountValidation::class);
        /**
         * @var AccountValidation $validation
         */
        $validation = $validationEntityManager->findOneBy(["code" => $validationCode]);
        if (!$validation) {
            throw new Exception("Validation code not found");
        }
        return $validation;
    }


    public function attachValidattionToUser(User $user): AccountValidation
    {

        $token          = $this->generateValidationCode(); // 64 caractères aléatoires
        $expirationDate = (new \DateTime())->modify("+1 hour");
        $validation  = new AccountValidation();
        $validation->setCode($token);
        $validation->setExpiredAt($expirationDate);
        $accountValidation = $this->entityManager->getRepository(AccountValidation::class);
        try {
            $this->entityManager->persist($validation);
            $this->entityManager->flush();
            $validationNew = $accountValidation->findOneBy(["code" => $token]);
        } catch (Exception $e) {
            $this->logger->error("Error persisting validation code: " . $e->getMessage());
            throw $e;
        }
        return $validationNew;
    }

    public function generateValidationCode(): String
    {
        return bin2hex(random_bytes(16));  // Code plus court
    }

    public function validateAccount(string $token): User
    {

        /**
         * @var AccountValidation $userAccountValidation
         */
        $userAccountValidation = $this->findAccountValidationByCode($token);
        if ($userAccountValidation) {
            /**
             * @var User $user
             */
            $user  = $userAccountValidation->getRelateduservalidation();
            if ($this->validationHasNotExpired($token) && !$user->getIsActive()) {
                $user->SetIsActive(true);
                $userAccountValidation->setActivationDate(new \DateTimeImmutable());
                $this->entityManager->persist($user);
                $this->entityManager->persist($userAccountValidation);
                $this->entityManager->flush();
            }
            return $user;
        }

        return null;
    }


    public function validationHasNotExpired(string $token): bool
    {
        /**
         * @var AccountValidation $userAccountValidation
         */
        $userAccountValidation = $this->findAccountValidationByCode($token);
        return $userAccountValidation->getExpiredAt() > new DateTime();
    }
}
