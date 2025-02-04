<?php

namespace App\Service;

use App\Entity\User;
use App\Message\UserKeycloakMessage;
use App\Service\HttpService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;

class KeycloakService
{
    const GRANT_TYPE_CREDENTIAL = 'client_credentials';
    const GRANT_TYPE_DIRECT_ACCESS_GRANT = 'password';
    const MANAGE_USER           = '/admin/realms/real__name/users';
    const GET_AUTH_TOKEN        = '/realms/real__name/protocol/openid-connect/token';
    const GET_ROLES             = '/admin/realms/realm__name/clients/client__id/roles';
    const GET_CLIENTS           = '/admin/realms/realm__name/clients';
    const ADD_ROLE_TO_USER      = '/admin/realms/realm__name/users/user__id/role-mappings/clients/client__id';
    const USER_INFO             = '/realms/realm__name/protocol/openid-connect/userinfo';
    const REALM_ROLES           = '/admin/realms/%s/roles';
    const ADD_REALM_ROLE_USER   = '/admin/realms/%s/users/%s/role-mappings/realm';
    const SEND_EMAIL_TO_USER    = '/admin/realms/%s/users/%s/execute-actions-email';
    const UPDATE_USER           = '/realms/{realm}/users/%s';
    // http://localhost:8080/realms/classbridge/protocol/openid-connect/userinfo

    public function __construct(
        private readonly HttpService $httpService,
        private LoggerInterface $logger,
        private ParameterBagInterface $params
    ) {}

    public function getAccessToken(): string
    {
        $realm        = $this->params->get("app.keycloak.realm");
        $clientId     = $this->params->get("app.keycloak.clientId");
        $clientSecret = $this->params->get("app.keycloak.clientSecret");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");

        if (!$realm || !$clientId || !$clientSecret || !$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        $url  = $keycloakHost . str_replace('real__name', $realm, self::GET_AUTH_TOKEN);

        $data = [
            'grant_type'     => self::GRANT_TYPE_CREDENTIAL,
            'client_id'      => $clientId,
            'client_secret'  => $clientSecret
        ];
    
        try {
            // Envoi de la requête pour obtenir un token
            $response = $this->httpService->postData($url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $data,
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data['access_token'] ?? null;
            }
        } catch (Exception $e) {
            // Gestion des erreurs
            throw new \RuntimeException('Error contacting Keycloak: ' . $e->getMessage());
        }
    }

    public function addUser(UserKeycloakMessage $message): UserKeycloakMessage
    {

        $realm        = $this->params->get("app.keycloak.realm");
        $clientId     = $this->params->get("app.keycloak.clientId");
        $clientSecret = $this->params->get("app.keycloak.clientSecret");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");
        $token        = $this->getAccessToken();


        if (!$realm || !$clientId || !$clientSecret || !$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        if (!$token) {
            $this->logger->error('Missing Token');
            throw new Exception('Missing Token');
        }

        $url  = $keycloakHost . str_replace('real__name', $realm, self::MANAGE_USER);
        $userData = [
            'username' => $message->getUsername(),
            'email' => $message->getEmail(),
            'firstName' => $message->getFirstName(),
            'lastName' => $message->getLastName(),
            'enabled' => $message->getEnabled(),
            'emailVerified' => $message->getEmailVerified(),
            'credentials' => [
                [
                    'type' => 'password',
                    'value' => $message->getPassword(),
                    'temporary' => false,
                ],
            ],
        ];

        $token    = $this->getAccessToken();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => $userData,
        ];

        try {

            $options =
                $response = $this->httpService->postData($url, $options);
            if ($response->getStatusCode() === 201) {
                return $message;
            }
            return null;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }


    public function getClients(?string $clientId = null): ?array
    {
        $realm        = $this->params->get("app.keycloak.realm");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");
        $token        = $this->getAccessToken();


        if (!$realm || !$keycloakHost || !$clientId) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        if (!$token) {
            $this->logger->error('Missing Token');
            throw new Exception('Missing Token');
        }

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ];
        $url  = $keycloakHost . str_replace('realm__name', $realm, self::GET_CLIENTS);
        if ($clientId) {
            $url = $url . "?clientId=" . $clientId;
        }


        try {
            $response = $this->httpService->getData($url, $options);
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data[0] ?? null;
            }
        } catch (TransportException $e) {
            $this->logger->error('Error contacting Keycloak: ' . $e->getMessage());
            throw new Exception('Error contacting Keycloak: ' . $e->getMessage());
        }
    }

    public function getClientId(string $clientId): ?string
    {

        return is_array($this->getClients($clientId)) ? $this->getClients($clientId)["id"] : null;
    }



    public function getRealmRoles()
    {
        $realm        = $this->params->get("app.keycloak.realm");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");
        $token        = $this->getAccessToken();


        if (!$realm || !$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        if (!$token) {
            $this->logger->error('Missing Token');
            throw new Exception('Missing Token');
        }

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ];

        $url  = $keycloakHost . sprintf(self::REALM_ROLES, $realm);
        try {
            $response = $this->httpService->getData($url, $options);
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data ?? null;
            }
            return null;
        } catch (TransportException $e) {
            $this->logger->error('Error contacting Keycloak: ' . $e->getMessage());
            throw new Exception('Error contacting Keycloak: ' . $e->getMessage());
        }
    }


    public function getUserByfilter(string $filter)
    {

        $realm        = $this->params->get("app.keycloak.realm");
        $clientId     = $this->params->get("app.keycloak.clientId");
        $clientSecret = $this->params->get("app.keycloak.clientSecret");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");
        $token        = $this->getAccessToken();


        if (!$realm || !$clientId || !$clientSecret || !$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        if (!$token) {
            $this->logger->error('Missing Token');
            throw new Exception('Missing Token');
        }

        $url  = $keycloakHost . str_replace('real__name', $realm, self::MANAGE_USER) . "?" . $filter;

        $token    = $this->getAccessToken();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ];

        try {


            $response = $this->httpService->getData($url, $options);

            if ($response->getStatusCode() === 200) {
                return $response->toArray()[0] ?? null;
            }
            return null;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }



    public function assignRolesToAuser(array $roles, string $userName)
    {


        $realm        = $this->params->get("app.keycloak.realm");
        $clientId     = $this->params->get("app.keycloak.clientId");
        $clientSecret = $this->params->get("app.keycloak.clientSecret");
        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");
        $token        = $this->getAccessToken();


        if (!$realm || !$clientId || !$clientSecret || !$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }
        $userId    = $this->getUserByfilter("username=" . $userName)["id"];
        if (!$token) {
            $this->logger->error('Missing Token');
            throw new Exception('Missing Token');
        }

        $url  = sprintf($keycloakHost . self::ADD_REALM_ROLE_USER, $realm, $userId);
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => $roles,
        ];
        try {
            $response = $this->httpService->postData($url, $options);
            if ($response->getStatusCode() == Response::HTTP_NO_CONTENT) {
                return [
                    "message" => "the roles was affected successfully"
                ];
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }


    /**
     *  activer le type Direct access Grant sur keycloak pour utiliser cette fonction
     */
    public function authenticateGrantTypePassword(
        string $username,
        string $password,
        string $realm,
        string $clientId,
        string $clientSecret
    ) {

        $keycloakHost = $this->params->get("app.keycloak.keycloakUri");

        if (!$keycloakHost) {
            $this->logger->error('Missing Keycloak configuration parameters');
            throw new Exception('Missing Keycloak configuration parameters');
        }

        $url  = $keycloakHost . str_replace('real__name', $realm, self::GET_AUTH_TOKEN);

        $data = [
            'grant_type'     => self::GRANT_TYPE_DIRECT_ACCESS_GRANT,
            'client_id'      => $clientId,
            'client_secret'  => $clientSecret,
            'username'       => $username,
            'password'       => $password
        ];

        try {
            // Envoi de la requête pour obtenir un token
            $response = $this->httpService->postData($url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $data,
            ]);
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data ?? null;
            }
            // Vérification du statut de la réponse et récupération du token
        } catch (TransportException $e) {
            // Gestion des erreurs
            throw new Exception('Error contacting Keycloak: ' . $e->getMessage());
        }
    }

    public function getUserInfo(string $token)
    {

        $realm        = $this->params->get("app.keycloak.realm");
        $keycloakHost = "http://keycloak.example.com";                   #$this->params->get("app.keycloak.keycloakUri");
        $url          = $keycloakHost . str_replace('realm__name', $realm, self::USER_INFO);
        $options = [ "auth_bearer" => $token ];

        try {
            $response = $this->httpService->postData($url, $options);
            dd( $url, $response->getStatusCode());

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data;
            }
            // Vérification du statut de la réponse et récupération du token
        } catch (Exception $e) {
            // Gestion des erreurs
            throw new Exception('Error contacting Keycloak: ' . $e->getMessage());
            $this->logger->error($e->getMessage());
        }
    }


    public function updateUser()
    {
        dd($this->getAccessToken());
    }
}
