<?php

namespace App\Controller;

use App\Service\AccountValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use ApiPlatform\OpenApi\OpenApi;
use App\Entity\User;
use App\Message\RoleMessage;
use App\Service\KeycloakService;
use App\Service\UserService;
use GuzzleHttp\Client;

class UserController extends AbstractController
{


    private $client;

    public function __construct(
        private AccountValidationService $accountValidationService,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus,
        private readonly LoggerInterface $loggerInterface,
        private readonly KeycloakService $keycloakService
    ) {
        $this->client = new Client();
    }

    #[Route('/validate-account/{token}', name: 'app_user_valiate_account', methods: ['GET'])]
    public function validateUserAccount(UserService $userService, string $token): Response
    {


        try {
            $user = $this->accountValidationService->validateAccount($token);
            // Send user to keycloak for asynchronous handler.
        } catch (Exception $e) {

            return new Response($e->getMessage(), 400);
            $this->loggerInterface->error($e->getMessage());
        }

        if ($user != null) {
            if ($user->getIsActive()) {
                $roles       = $userService->userRoleValidate($user);
                if (!empty($roles)) {
                    $roleMessage = new RoleMessage($roles, $user->getEmail());
                    $this->bus->dispatch($roleMessage);
                }
                return new Response("Votre compte est Activé et validé", Response::HTTP_OK);
            } else {
                return new Response("Code de validation expiré", Response::HTTP_OK);
            }
        }
        return new Response("code de validation invalide", Response::HTTP_NOT_FOUND);
    }




    #[Route('api/realms/{realm}/protocol/openid-connect/token', name: 'app_user_valiate_accountZ', methods: ['POST', 'GET'])]
    public function testController(Request $request, string $realm): Response
    {


        $roles = $this->keycloakService->getRealmRoles();

        $rolesList = array_map(static function ($data) {
            return [
                "name" => $data["name"],
                "id"   => $data["id"]
            ];
        }, $roles);



        $client =  $this->keycloakService->assignRolesToAuser($rolesList, "classbridge@mail.com");


        return new JsonResponse([
            "user"  => $client,
            "token" => "",
        ], 200);
    }


    #[Route("hello", methods: ["GET"])]
    public function index()
    {

        $accessToken = "";
        $decode = []; #$firebase::decode($accessToken);

        $response = [];
        return new JsonResponse([
            "user"  => $response,
            "token" => $accessToken,
        ], 200);
    }
}
