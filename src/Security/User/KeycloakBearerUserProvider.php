<?php


namespace App\Security\User;


use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Security\User\KeycloakBearerUser;
use App\Service\HttpService;
use App\Service\KeycloakService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class KeycloakBearerUserProvider implements UserProviderInterface{

    /**
     * @var string
     */
    private $issuer;
    /**
     * @var string
     */
    private $realm;
    /**
     * @var string
     */
    private $client_id;
    /**
     * @var string
     */
    private $client_secret;
    /**
     * @var mixed
     */
    private $sslVerification;
    public function __construct(private ParameterBagInterface $parameterBagInterface,
    private KeycloakService $keycloakService)
    {}

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API, which is our case), this
     * method is not called. But it is implement it anyway.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof KeycloakBearerUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user = $this->loadUserByIdentifier($user->getAccessToken());

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return KeycloakBearerUser::class === $class || is_subclass_of($class, KeycloakBearerUser::class);
    }

    /**
     * @param string $accessToken
     * @return UserInterface
     */
    public function loadUserByIdentifier(string $accessToken): UserInterface
    {

        $realm        = $this->parameterBagInterface->get("app.keycloak.realm");
        $client_id     = $this->parameterBagInterface->get("app.keycloak.clientId");
        $client_secret = $this->parameterBagInterface->get("app.keycloak.clientSecret");
        $keycloakHost = $this->parameterBagInterface->get("app.keycloak.keycloakUri");



        try{

            $userInfo = $this->keycloakService->getUserInfo($accessToken);
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new CustomUserMessageAuthenticationException('Unable to fetch user information from Keycloak');
        }

        $jwt = json_decode('{}', true);
        dd( $jwt );

        if (!$jwt['active']) {
            throw new CustomUserMessageAuthenticationException('The token does not exist or is not valid anymore');
        }

        if (!isset($jwt['resource_access'][$client_id])) {
            throw new CustomUserMessageAuthenticationException('The token does not have the necessary permissions!');
        }

        return new KeycloakBearerUser(
            $jwt['sub'],
            $jwt['name'],
            $jwt['email'],
            $jwt['given_name'],
            $jwt['family_name'],
            $jwt['preferred_username'],
            $jwt['resource_access'][$client_id]['roles'],
            $accessToken
        );
    }

    /**
     * @param string $username
     * @return UserInterface
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }
}