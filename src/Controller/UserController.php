<?php

namespace App\Controller;

use App\Entity\ParentEntity;
use App\Entity\User;
use App\Service\AccountValidationService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Security\User as UserKeyCloak;
use App\Message\RoleMessage;
use App\Service\KeycloakService;
use App\Service\UserService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;


class UserController extends AbstractController
{




    public function __construct(
        private AccountValidationService $accountValidationService,
        private MessageBusInterface $bus,
        private LoggerInterface $loggerInterface,
        private KeycloakService $keycloakService
    ) {}

    #[Route('/validate-account/{token}', name: 'app_user_valiate_account', methods: ['GET'])]
    public function validateUserAccount(UserService $userService, string $token): Response
    {


        try {
            $user = $this->accountValidationService->validateAccount($token);
            // Send user to keycloak for asynchronous handler.
        } catch (Exception $e) {
            $this->loggerInterface->error($e->getMessage());
            return new Response($e->getMessage(), 400);
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
    public function index(): JsonResponse
    {

        $accessToken = "";
        $decode = []; #$firebase::decode($accessToken);

        $response = [];
        return new JsonResponse([
            "user"  => $response,
            "token" => $accessToken,
        ], 200);
    }

    //#[IsGranted( new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") or is_granted("ROLE_TEACHER ")') )]
    #[IsGranted(new Expression('is_authenticated()'))]
    #[Route("api/user/getMe",  name: "get_user_me", methods: ["GET"])]
    public function __invoke(UserService $userService): JsonResponse
    {


        /** @var UserKeyCloak $user */
        $user         = $this->getUser();
        $userData     = [];
        if ($user) {
            $reponse = $userService->getUserByType($user);
            return $this->json($reponse, Response::HTTP_OK);
        }
        return new JsonResponse($userData, Response::HTTP_NOT_FOUND);
    }
}
