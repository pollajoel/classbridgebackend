<?php


namespace App\Consumer;

use App\Message\RoleMessage;
use App\Service\KeycloakService;
use App\Service\UserService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class RoleMessageHandler {

    public function __construct(private readonly UserService $userService,private readonly KeycloakService $keycloakService) {}

    public function __invoke(RoleMessage $message) {
        // Process the role message...
        $this->keycloakService->assignRolesToAuser($message->getRoles(), $message->getUsername());
    }
}