<?php

namespace App\Consumer;

use App\Service\KeycloakService;
use App\Message\UserKeycloakMessage;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class UserKeycloakMessageHandler
{

    public function __construct(
        private  KeycloakService $keycloakService,
        private LoggerInterface $loggerInterface
    ) {}

    public function __invoke(UserKeycloakMessage $user)
    {

        $this->loggerInterface->error("hello world!");
        // try {
        //     $keycloakUserNew  = $this->keycloakService->addUser($user);
        //     if( !$keycloakUserNew ){
        //         $this->loggerInterface->info("Error Adding user on keycloak...");
        //     }else{
        //         $this->loggerInterface->info("User Successfull add on keycloak..");
        //     }
        // } catch (Exception $e) {
        //     $this->loggerInterface->error($e->getMessage());
        // }
    }
}
