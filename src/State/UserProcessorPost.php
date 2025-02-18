<?php
// api/src/State/UserPasswordHasher.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Entity\ParentEntity;
use App\Entity\TeacherEntity;
use App\Entity\StudentEntity;
use App\Service\AccountValidationService;
use App\Service\MailerService;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\AccountValidation;
use App\Service\KeycloakService;
use App\Service\UserService;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<User, User|void>
 */
final readonly class UserProcessorPost implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerService $mailerService,
        private AccountValidationService $accountValidationService,
        private readonly UserService $userService,
        private readonly KeycloakService $keycloakService,
        private MessageBusInterface $bus
    )
    {
    }

    /**
     * @param User | ParentEntity | TeacherEntity | StudentEntity  $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []):mixed
    {
        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $this->userService->userRoleValidate($data);
        /**
         * Hash the password before persisting it in the database
         */
        $hashedPassword = $this->userService->hashPassword($data);
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();
        try{
           /**
            *  @var AccountValidation $validation
            */
            $accountValidation = $this->accountValidationService->attachValidattionToUser($data);
            # créer new validation for User.
            $data->addAccountsValidation( $accountValidation );
        }catch( Exception $e ){
            throw new Exception($e->getMessage());
        }
       
        try{
            $this->mailerService->sendValidationEmail($data, $accountValidation);
            # création de l'utilisateur
            $this->userService->createUserOnKeycloak($data);

        }catch(Exception $e){
            throw new Exception($e->getMessage()); 
        }
        /** @var User $result */
        $result = $this->processor->process($data, $operation, $uriVariables, $context);
        return $result;
    }

}
