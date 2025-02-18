<?php


namespace App\Consumer;

use App\Message\RoleMessage;
use App\Service\KeycloakService;
use App\Service\UserService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class RoleMessageHandler {

    public function __construct(private KeycloakService $keycloakService) {}

    public function __invoke(RoleMessage $message):void {
        // Process the role message...
        $this->keycloakService->assignRolesToAuser($message->getRoles(), $message->getUsername());
        return;
    }
}