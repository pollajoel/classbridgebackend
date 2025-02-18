<?php
declare( strict_types=1);

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use App\Service\KeycloakService;
use Symfony\Component\HttpFoundation\JsonResponse;


#[AsController]
#[ApiResource()]
  class ApiLoginController extends AbstractController
  {

   public function __construct(private KeycloakService $keycloakService)
   {
      
   }
   //  #[Route('/', name: 'api_login_check_keyckloak', methods: ['POST', 'GET'])]
   //  public function connectAction(Request $request, ClientRegistry $clientRegistry):RedirectResponse
   //   {

   //    $code = $request->query->get('code');
   //    if( !$code ){
   //       return $clientRegistry->getClient('keycloak')->redirect(['openid', 'profile', 'email'], []);
   //    }


     //}

     #[Route('/api/login/check-keycloak', name: 'api_login_check_keyckloak_1', methods: ['POST', 'GET'])]
     public function loginCheckKeycloak(Request $request, ClientRegistry $clientRegistry): JsonResponse
      {
       // Récupérer l'utilisateur après l'authentification réussie

       $data = $this->keycloakService->authenticateGrantTypePassword("classbridge@mail.com","test", "classbridge", "classbridge-backend", "KvYXsKtzqv1jm91nJCoc8VhJqKLKsbY7");
         
         return new JsonResponse(["token"=> $data], 200);
      }
 

     
     #[Route('/api/login', name: 'keycloak_check', methods: ['POST', 'GET', 'PUT', 'DELETE'])]
     public function index(#[CurrentUser] ?User $user, Request $request): Response
      {
        dd( $request->headers);
    
        //  if (null === $user) {
        //      return $this->json([
        //          'message' => 'missing credentials',
        //          'user'   => $this->getUser(),
        //      ], Response::HTTP_UNAUTHORIZED);
        //  }

         //$token = ...; // somehow create an API token for $user

          return $this->json([
             'message' => 'Welcome to your new controller!',
             'path' => 'src/Controller/ApiLoginController.php',
             'user'  => $user->getUserIdentifier(),
             'token' => $token = "",
          ]);
      }
  }